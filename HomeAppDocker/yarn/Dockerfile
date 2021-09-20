FROM node:12

COPY docker-entrypoint.sh /usr/bin/docker-entrypoint
RUN chmod +x /usr/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]

# CMD ["yarn"]
CMD ["yarn", "dev-server", "--host=apache"]

EXPOSE 8080
