FROM php:7.3.28-alpine

ADD ./docker/jobs/crontab /crontab
RUN /usr/bin/crontab /crontab

CMD ["/usr/sbin/crond","-f","-l","8"]
