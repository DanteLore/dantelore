Manual config done first, following this (great) tutorial:
https://www.davidbaumgold.com/tutorials/host-static-site-aws-s3-cloudfront/

To deploy the website:

```
$ aws configure --profile dantelore
$ cd terraform
$ terraform plan
$ terraform apply
$ cd ../hugo
$ hugo
$ aws --profile dantelore s3 sync public s3://www.dantelore.com
```

https://docs.aws.amazon.com/Route53/latest/DeveloperGuide/domain-replace-hosted-zone.html
Note that once the hosted zone is created, the nameservers on the *domain* need
to be updated to point to it (not the other way round!).

Much of the terraform stuff came from here:
https://gist.github.com/nagelflorian/67060ffaf0e8c6016fa1050b6a4e767a

With this handy tip for referencing the S3 website from CloudFront via terraform:
https://kupczynski.info/2017/03/06/terraform-cloudfront-s3-static-hosting.html
