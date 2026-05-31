# frontend/Dockerfile
FROM node:24-alpine

WORKDIR /app

# Instala Angular CLI globalmente
RUN npm install -g @angular/cli

# Copia los archivos de dependencias primero (mejor cache de Docker)
COPY package*.json ./

# Instala dependencias
RUN npm install

# Copia el resto del código
COPY . .

# Expone el puerto de ng serve
EXPOSE 4200

# Arranca el servidor de desarrollo
# --host 0.0.0.0 es imprescindible para que sea accesible desde fuera del contenedor
CMD ["ng", "serve", "--host", "0.0.0.0", "--poll", "2000"]
