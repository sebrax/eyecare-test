FROM node:18-alpine

# ----
# 1 - Define o diretório de trabalho dentro do container
# ----
WORKDIR /app

# ----
# 2 - Copia os arquivos package.json e package-lock.json para instalação das dependências
# ----
COPY package*.json ./

# ----
# 3 - Instala as dependências Node definidas no package.json
# ----
RUN npm install

# ----
# 4 - Copia todo o restante do código da aplicação para dentro do container
# ----
COPY . .

# ----
# 5 - Expõe a porta 8080 para o servidor de desenvolvimento do frontend
# ----
EXPOSE 8080

# ----
# 6 - Comando padrão ao iniciar o container: roda o script "serve" do package.json (normalmente servidor dev)
# ----
CMD ["npm", "run", "serve"]
