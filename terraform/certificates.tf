provider "aws" {
  alias = "virginia"
  region = "us-east-1"
  shared_credentials_file = "~/.aws/credentials"
  profile = var.aws_profile
}

resource "aws_acm_certificate" "cert" {
  count = length(var.domain_names)
  provider = aws.virginia
  domain_name = var.domain_names[count.index]
  subject_alternative_names = ["*.${var.domain_names[count.index]}"]
  validation_method = "EMAIL"

  lifecycle {
    create_before_destroy = true
  }
}

resource "aws_acm_certificate_validation" "cert" {
  count = length(var.domain_names)
  certificate_arn = aws_acm_certificate.cert[count.index].arn
  provider = aws.virginia
}
