FROM skytsar/nginx
VOLUME /app
COPY . /app
CMD /bin/true
