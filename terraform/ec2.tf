data "aws_ssm_parameter" "ecs_ami" {
  name = "/aws/service/ecs/optimized-ami/amazon-linux-2/recommended"
}

resource "aws_instance" "cluster_instance" {
  ami           = jsondecode(data.aws_ssm_parameter.ecs_ami.value).image_id
  instance_type = "t3.small"
  security_groups = [
    aws_security_group.ktcms_dev_db.id,
    aws_security_group.ktcms_dev_ktcms.id,
    aws_security_group.ktcms_dev_phpmyadmin.id,
  ]
  iam_instance_profile = aws_iam_instance_profile.ec2_container_service_role.name
  subnet_id            = aws_subnet.public.id

  root_block_device {
    volume_type           = "gp2"
    volume_size           = "30"
    delete_on_termination = true
    encrypted             = false
  }

  user_data = <<EOF
#!/bin/bash
echo ECS_CLUSTER=${aws_ecs_cluster.main.name} >> /etc/ecs/ecs.config;
EOF

  tags = {
    Name = "ktcms-dev-cluster-instance"
  }
}

resource "aws_eip" "cluster_instance" {
  instance = aws_instance.cluster_instance.id
  vpc      = true

  tags = {
    Name = "ktcms-dev-cluster-instance"
  }
}
