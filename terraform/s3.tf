resource "aws_s3_bucket" "main" {
  bucket_prefix = "ktcms-dev"
}

resource "aws_s3_object" "ktcms_env" {
  bucket = aws_s3_bucket.main.id
  key    = "ktcms.env"
}
