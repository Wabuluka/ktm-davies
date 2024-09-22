resource "aws_ecr_repository" "ktcms" {
  name = "ktcms_dev_ktcms"
}

resource "aws_ecr_lifecycle_policy" "ktcms" {
  policy = jsonencode(
    {
      "rules" : [
        {
          "rulePriority" : 1,
          "description" : "Hold only 2 images, expire all others",
          "selection" : {
            "tagStatus" : "any",
            "countType" : "imageCountMoreThan",
            "countNumber" : 2,
          },
          "action" : {
            "type" : "expire"
          }
        }
      ]
    }
  )

  repository = aws_ecr_repository.ktcms.name
}
