wget -q -O - http://www.xunsearch.com/scws/down/scws-1.2.1.tar.bz2 | tar xjf -
cd scws-1.2.1 ; ./configure --prefix=/home/work/local/scws-1.2.1; make install
cd phpext
phpize
./configure --with-scws=/home/work/local/scws-1.2.1 --with-php-config=/home/work/local/php/bin/php-config
make && make install
