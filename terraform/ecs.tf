resource "aws_ecs_cluster" "main" {
  name = "ktcms-dev"
}

resource "aws_ecs_task_definition" "main" {
  family                   = "ktcms-dev"
  requires_compatibilities = ["EC2"]
  network_mode             = "bridge"
  execution_role_arn       = aws_iam_role.ecs_task_execution_role.arn
  container_definitions = jsonencode([
    {
      name  = "ktcms"
      image = "${aws_ecr_repository.ktcms.repository_url}:latest"
      portMappings = [{
        containerPort = 80
        hostPort      = 80
        protocol      = "tcp"
      }]
      memory            = 1024
      memoryReservation = 1024
      essential         = true
      environmentFiles = [{
        value = "${aws_s3_bucket.main.arn}/${aws_s3_object.ktcms_env.key}"
        type  = "s3"
      }]
      links = ["db"]
    },
    {
      name  = "db"
      image = "public.ecr.aws/docker/library/mariadb:10"
      portMappings = [{
        containerPort = 3306
        hostPort      = 3306
        protocol      = "tcp"
      }]
      memory            = 256
      memoryReservation = 256
      essential         = true
      environmentFiles = [{
        value = "${aws_s3_bucket.main.arn}/${aws_s3_object.ktcms_env.key}"
        type  = "s3"
      }]
      mountPoints = [{
        "sourceVolume" : "db-store",
        "containerPath" : "/var/lib/mysql"
      }]
    },
    {
      name  = "phpmyadmin"
      image = "public.ecr.aws/docker/library/phpmyadmin:latest"
      portMappings = [{
        containerPort = 80
        hostPort      = 82
        protocol      = "tcp"
      }]
      memory            = 64
      memoryReservation = 64
      essential         = true
      environment = [{
        name  = "PMA_HOST"
        value = "db"
      }]
      links = ["db"]
    },
  ])

  volume {
    name = "db-store"
    docker_volume_configuration {
      scope         = "shared"
      autoprovision = true
    }
  }

  depends_on = [
    aws_ecr_repository.ktcms,
    aws_s3_object.ktcms_env,
  ]
}

resource "aws_ecs_service" "main" {
  name                               = "ktcms-dev"
  cluster                            = aws_ecs_cluster.main.id
  task_definition                    = aws_ecs_task_definition.main.arn
  deployment_minimum_healthy_percent = 0
  deployment_maximum_percent         = 100
  desired_count                      = 1
}
