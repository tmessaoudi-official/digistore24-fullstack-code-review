# Frontend Dockerfile for Angular Application
FROM node:24-alpine

# Set working directory
WORKDIR /app

# Copy package files
COPY .npmrc package*.json ./

# Install dependencies
RUN npm ci

# Copy application files
COPY . .

# Expose port
EXPOSE 4200

# Default command (overridden in docker-compose.yml)
CMD ["npm", "run", "start:development", "--", "--host", "0.0.0.0", "--port", "4200"]
