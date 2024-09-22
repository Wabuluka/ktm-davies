# KTCMS - Terraform

KTCMS のデプロイ先を構築するための Terraform です。\
（今のところレーベルサイト用のリソースは対象外）

## AWS リソースの構築手順

1. .env ファイルを作成

   ```
   AWS_ACCESS_KEY_ID=
   AWS_SECRET_ACCESS_KEY=
   ```

1. terraform.tfvars ファイルを作成

   ```
   github_owner           = ""
   github_repository_name = ""
   ```

1. Terraform コンテナを起動

   ```bash
   docker compose up -d
   docker compose exec terraform sh
   ```

1. Terraform コマンドを実行

   ```bash
   terraform init
   terraform apply
   ```

## GitHub Actions シークレット 設定

### デプロイ用シークレット

デプロイ時に必要となる下記環境変数を GitHub Actions のシークレットに設定する

- ECR_REPOSITORY_KTCMS
- ECS_CLUSTER
- ECS_SERVICE
- IAM_ROLE_ARN
- TASK_DEFINITION

環境変数の値は、AWS リソース構築後、Terraform の Output から確認できる

```bash
terraform output

# JSON 形式で確認する場合
terraform output -json > outputs.json
```

### BASIC 認証設定用シークレット

BASIC 認証情報の設定に必要となる下記環境変数を GitHub Actions のシークレットに設定する

- BASIC_USER
- BASIC_PASS

任意のユーザ名・パスワードを設定してよい
