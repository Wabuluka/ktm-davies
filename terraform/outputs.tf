output "iam_role_arn" {
  value = aws_iam_role.github.arn
}

output "ecr_repository_ktcms" {
  value = aws_ecr_repository.ktcms.repository_url
}

output "s3_ktcms_env_file_arn" {
  value = "${aws_s3_bucket.main.arn}/${aws_s3_object.ktcms_env.key}"
}

output "ecs_cluster" {
  value = aws_ecs_cluster.main.name
}

output "ecs_service" {
  value = aws_ecs_service.main.name
}

output "task_definition" {
  value = aws_ecs_task_definition.main.family
}

output "public_dns" {
  value = aws_eip.cluster_instance.public_dns
}
