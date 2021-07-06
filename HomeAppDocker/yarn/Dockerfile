FROM node:12

COPY docker-entrypoint.sh /usr/bin/docker-entrypoint
RUN chmod +x /usr/bin/docker-entrypoint

# RUN npm install yarn -g

ENTRYPOINT ["docker-entrypoint"]

CMD ["yarn"]
# CMD ["yarn", "dev-server", "--host=0.0.0.0"]

EXPOSE 8080
