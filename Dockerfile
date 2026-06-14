# Usa uma imagem oficial do PHP com Apache integrado
FROM php:8.2-apache

# Instala as dependências necessárias para o cURL funcionar no PHP
RUN apt-get update && apt-get install -y libcurl4-openssl-dev pkg-config ssl-cert && rm -rf /var/lib/apt/lists/*

# Ativa a extensão cURL no PHP
RUN docker-php-ext-install curl

# Copia todos os arquivos do seu repositório para a pasta do servidor Apache
COPY . /var/www/html/

# Expõe a porta padrão do Apache
EXPOSE 80
