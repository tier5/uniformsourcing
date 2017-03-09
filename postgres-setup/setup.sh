#!/bin/sh -e

sudo yum -y update
echo "******** installing apache2/httpd *******"
sudo yum -y install httpd
echo "******** starting service httpd *********"
sudo service httpd start
echo "******** starting service httpd *********"
sudo systemctl enable httpd.service
echo "******** enabling service httpd *********"

#sudo yum -y install postgresql postgresql-contrib postgresql-doc

#sudo rpm -ivh http://yum.postgresql.org/9.4/redhat/rhel-7-x86_64/pgdg-centos94-9.4-1.noarch.rpm

echo " ************* installing nano *************"
sudo yum -y install nano
echo " ************* adding postgres 9.4.11 repository *************"
sudo rpm -ivh https://download.postgresql.org/pub/repos/yum/9.4/redhat/rhel-7-x86_64/pgdg-centos94-9.4-3.noarch.rpm
echo " ************* installing postgresql server **********"
sudo yum install postgresql94-server postgresql94-contrib -y
echo " ************* initializing database *****************"
sudo /usr/pgsql-9.4/bin/postgresql94-setup initdb
echo " ************* listing postgres files ****************"
sudo systemctl list-unit-files |grep postgres
echo " ************* enabling postgres 9.4 *************"
sudo systemctl enable postgresql-9.4.service
echo "postgres service enabled ******** rewriting postgresql.conf file ********"

#sudo chown vagrant:vagrant /var/lib/pgsql /var/lib/pgsql/9.4 /var/lib/pgsql/9.4/data /var/lib/pgsql/9.4/data/postgresql.conf

echo "write complete **** restarting postgres"
sudo systemctl restart postgresql-9.4.service

sudo yum -y install wget
sudo yum -y update
echo "******** update complete ***********"
sudo yum -y install epel-release
echo "******** initializing epel release ***********"
sudo wget https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
echo "******** downloading noarch 7 *********"
sudo wget https://centos7.iuscommunity.org/ius-release.rpm
echo "******** downloading other files ******"
sudo rpm -Uvh ius-release*.rpm
echo "******** updating *******"
sudo yum -y update
echo "******** installing php 5.6 ***********"
sudo yum -y install php56u php56u-opcache php56u-xml php56u-mcrypt php56u-gd php56u-devel php56u-pgsql php56u-intl php56u-mbstring php56u-bcmath php56-bz2 php56u-gmp php56u-sqlite3 php56u-zip


#ln -s '/usr/lib/systemd/system/postgresql-9.4.service' '/etc/systemd/system/multi-user.target.wants/postgresql-9.4.service'