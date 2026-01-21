# ========== Build Stage ==========

FROM node:22-alpine AS builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# ========== Run Stage ==========
FROM alpine

WORKDIR /usr/share/nginx/html

COPY --from=builder /app/dist .

CMD ["sh", "-c", "sleep infinity"]