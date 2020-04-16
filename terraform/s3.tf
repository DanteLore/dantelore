provider "aws" {
    profile = var.aws_profile
    region = var.aws_region
}

resource "aws_s3_bucket" "prod_bucket" {
    bucket = var.bucket_name
    acl = "public-read"
    force_destroy = true
    policy = <<EOF
{
    "Version": "2008-10-17",
    "Statement": [
        {
            "Sid": "AllowPublicRead",
            "Effect": "Allow",
            "Principal": {
                "AWS": "*"
            },
            "Action": "s3:GetObject",
            "Resource": "arn:aws:s3:::${var.bucket_name}/*"
        }
    ]
}
EOF

  website {
    index_document = "index.html"
    error_document = "error.html"
  }
}
