variable "aws_profile" {}
variable "aws_region" {}
variable "bucket_name" {}
variable "domain_name" {}
variable "email_server_1" {}
variable "email_server_2" {}
variable "email_txt_record" {}
variable "google_txt_record" {}

variable "domain_names" { type = list(string) }
variable "domain_subdirectories" { type = list(string) }
variable "certificates" { type = list(string) }
