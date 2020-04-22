resource "aws_route53_zone" "main" {
  count = length(var.domain_names)
  name = var.domain_names[count.index]
  comment = "Managed by Terraform"
}

resource "aws_route53_record" "main-a-record" {
   zone_id = aws_route53_zone.main[count.index].zone_id
   count = length(var.domain_names)
   name = var.domain_names[count.index]
   type = "A"
   alias {
    name = aws_cloudfront_distribution.s3_distribution[count.index].domain_name
    zone_id = aws_cloudfront_distribution.s3_distribution[count.index].hosted_zone_id
    evaluate_target_health = false
  }
}

resource "aws_route53_record" "mx" {
  zone_id = aws_route53_zone.main[count.index].zone_id
  count = length(var.domain_names)
  name = var.domain_names[count.index]
  type = "MX"
  ttl = "300"

  records = [
    "10 ${var.email_server_1}",
    "20 ${var.email_server_2}"
  ]
}

resource "aws_route53_record" "txt" {
  zone_id = aws_route53_zone.main[count.index].zone_id
  count = length(var.domain_names)
  name = var.domain_names[count.index]
  type = "TXT"
  ttl = "300"

  records = [
    "${var.email_txt_record}",
    "${var.google_txt_record}"
  ]
}

resource "aws_route53_record" "main-c-name" {
  zone_id = aws_route53_zone.main[count.index].zone_id
  count = length(var.domain_names)
  name = "www"
  type = "CNAME"
  ttl = "300"
  records = ["${var.domain_names[count.index]}"]
}
