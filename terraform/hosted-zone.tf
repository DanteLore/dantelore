resource "aws_route53_zone" "main" {
  name = var.domain_name
  comment = "Managed by Terraform"
}

resource "aws_route53_record" "main-a-record" {
   zone_id = aws_route53_zone.main.zone_id
   name = var.domain_name
   type = "A"
   alias {
    name = aws_cloudfront_distribution.s3_distribution.domain_name
    zone_id = aws_cloudfront_distribution.s3_distribution.hosted_zone_id
    evaluate_target_health = false
  }
}

resource "aws_route53_record" "main-c-name" {
  zone_id = aws_route53_zone.main.zone_id
  name = "www"
  type = "CNAME"
  ttl = "300"
  records = ["${var.domain_name}"]
}

resource "aws_route53_record" "mx" {
  zone_id = aws_route53_zone.main.zone_id
  name = var.domain_name
  type = "MX"
  ttl = "300"

  records = [
    "10 ${var.email_server_1}",
    "20 ${var.email_server_2}"
  ]
}

resource "aws_route53_record" "txt" {
  zone_id = aws_route53_zone.main.zone_id
  name = var.domain_name
  type = "TXT"
  ttl = "300"

  records = [
    "${var.email_txt_record}"
  ]
}
