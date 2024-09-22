# EC2
data "aws_iam_policy_document" "ec2_assume_role" {
  statement {
    actions = ["sts:AssumeRole"]

    principals {
      type        = "Service"
      identifiers = ["ec2.amazonaws.com"]
    }
  }
}

resource "aws_iam_role" "ec2_container_service_role" {
  name_prefix        = "ec2_container_service_role"
  assume_role_policy = data.aws_iam_policy_document.ec2_assume_role.json
}

resource "aws_iam_role_policy_attachment" "ec2_container_service_role" {
  role       = aws_iam_role.ec2_container_service_role.name
  policy_arn = "arn:aws:iam::aws:policy/service-role/AmazonEC2ContainerServiceforEC2Role"
}

resource "aws_iam_role_policy_attachment" "ssm_managed_instance_core" {
  role       = aws_iam_role.ec2_container_service_role.name
  policy_arn = "arn:aws:iam::aws:policy/AmazonSSMManagedInstanceCore"
}

resource "aws_iam_instance_profile" "ec2_container_service_role" {
  name_prefix = "ec2-container-service-role"
  role        = aws_iam_role.ec2_container_service_role.name
}

# ECS Task
data "aws_iam_policy_document" "ecs_assume_role" {
  statement {
    actions = ["sts:AssumeRole"]

    principals {
      type        = "Service"
      identifiers = ["ecs-tasks.amazonaws.com"]
    }
  }
}

resource "aws_iam_role" "ecs_task_execution_role" {
  name_prefix        = "ecs_task_execution_role"
  assume_role_policy = data.aws_iam_policy_document.ecs_assume_role.json
}

resource "aws_iam_role_policy_attachment" "ecs_task_execution_role_policy" {
  role       = aws_iam_role.ecs_task_execution_role.name
  policy_arn = "arn:aws:iam::aws:policy/service-role/AmazonECSTaskExecutionRolePolicy"
}

resource "aws_iam_policy" "read_bucket" {
  name_prefix = "ktcms-dev-read-env"
  policy = jsonencode(
    {
      "Version" : "2012-10-17",
      "Statement" : [
        {
          "Effect" : "Allow",
          "Action" : [
            "s3:GetObject"
          ],
          "Resource" : [
            "${aws_s3_bucket.main.arn}/*"
          ]
        },
        {
          "Effect" : "Allow",
          "Action" : [
            "s3:GetBucketLocation"
          ],
          "Resource" : [
            "${aws_s3_bucket.main.arn}"
          ]
        }
      ]
    }
  )
}

resource "aws_iam_role_policy_attachment" "ktcms_dev_read_env" {
  role       = aws_iam_role.ecs_task_execution_role.name
  policy_arn = aws_iam_policy.read_bucket.arn
}

# Github Actions CI/CD
data "http" "github_actions_openid_configuration" {
  url = "https://token.actions.githubusercontent.com/.well-known/openid-configuration"
}

data "tls_certificate" "github_actions" {
  url = jsondecode(data.http.github_actions_openid_configuration.response_body).jwks_uri
}

resource "aws_iam_role" "github" {
  name_prefix = "ktcms-dev-github"

  assume_role_policy = jsonencode(
    {
      "Version" : "2012-10-17",
      "Statement" : [
        {
          "Effect" : "Allow",
          "Principal" : {
            "Federated" : "arn:aws:iam::560130435964:oidc-provider/token.actions.githubusercontent.com"
          },
          "Action" : "sts:AssumeRoleWithWebIdentity",
          "Condition" : {
            "StringLike" : {
              "token.actions.githubusercontent.com:sub" : "repo:${var.github_owner}/${var.github_repository_name}:*"
            },
            "StringEquals" : {
              "token.actions.githubusercontent.com:aud" : "sts.amazonaws.com"
            }
          }
        }
      ]
    }
  )
}

resource "aws_iam_policy" "ecr_deploy" {
  name_prefix = "ktcms-dev-ecr-deploy"
  policy = jsonencode(
    {
      "Version" : "2012-10-17",
      "Statement" : [
        {
          "Effect" : "Allow",
          "Action" : [
            "ecr:GetAuthorizationToken",
          ],
          "Resource" : "*"
        },
        {
          "Effect" : "Allow",
          "Action" : [
            "ecr:GetDownloadUrlForLayer",
            "ecr:BatchGetImage",
            "ecr:BatchCheckLayerAvailability",
            "ecr:PutImage",
            "ecr:InitiateLayerUpload",
            "ecr:UploadLayerPart",
            "ecr:CompleteLayerUpload"
          ],
          "Resource" : aws_ecr_repository.ktcms.arn
        }
      ]
    }
  )
}

resource "aws_iam_policy" "ecs_deploy" {
  name_prefix = "ktcms-dev-ecs-deploy"
  policy = jsonencode(
    {
      "Version" : "2012-10-17",
      "Statement" : [
        {
          "Effect" : "Allow",
          "Action" : [
            "ecs:DescribeTaskDefinition",
            "ecs:RegisterTaskDefinition",
          ],
          "Resource" : "*"
        },
        {
          "Effect" : "Allow",
          "Action" : [
            "ecs:UpdateService",
            "ecs:DescribeServices",
          ],
          "Resource" : "*"
        },
        {
          "Effect" : "Allow",
          "Action" : [
            "iam:PassRole"
          ],
          "Resource" : aws_iam_role.ecs_task_execution_role.arn
        }
      ]
    }
  )
}

resource "aws_iam_role_policy_attachment" "github_actions_ecr_deploy" {
  role       = aws_iam_role.github.name
  policy_arn = aws_iam_policy.ecr_deploy.arn
}

resource "aws_iam_role_policy_attachment" "github_actions_ecs_deploy" {
  role       = aws_iam_role.github.name
  policy_arn = aws_iam_policy.ecs_deploy.arn
}
