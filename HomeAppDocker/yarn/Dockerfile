FROM node:10.21.0

COPY docker-entrypoint.sh /usr/bin/docker-entrypoint
RUN chmod +x /usr/bin/docker-entrypoint

# RUN npm install yarn -g

ENTRYPOINT ["docker-entrypoint"]
CMD ["yarn"]

EXPOSE 8080