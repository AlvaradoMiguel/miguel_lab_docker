# Laboratorio Tecnologías de Virtualización - Wordpress en contenedor y aplicación en AWS ECS

![N|Solid](https://upload.wikimedia.org/wikipedia/commons/thumb/a/aa/Logo_DuocUC.svg/2560px-Logo_DuocUC.svg.png)

Con este laboratorio aprederás a contruir una Imágen de contenedor utilizando una Instancia de AWS como medio de trabajo, a esta instancia se le provisionan tanto del entorno de Docker y Git para gestionar la Imágen, y luego propagarla hacia el sistema AWS ECR, que se encargara de gestionar el contenedor y levantar el entorno que desplegará el contenido paquetizado en el contenedor. Para finalmente, montar esta infraestructura detras de un balanceador de carga que conectara el contenedor y una base de datos para levantar la aplicación de WordPress.

Utilizaremos lo siguientes recursos:
- Instancias EC2
- Dentro de la Instancia EC2 se debe instalar herramientas Docker y Git
- Grupos de Seguridad de AWS
- VPC de AWS
- Application Load Balancer de AWS
- ECS de AWS
- ECR de AWS
- Bases de datos RDS - En este caso, MySQL
- App Putty para conexiones SSH
- MariaDB para gestión de opciones de base datos RDS


## Pasos de creación de Infraestructura en Amazos AWS

Para el desarrollo de esta actividad se disponen de los siguientes materiales de trabajo a traves de un repositorio de GitHub
- Archivo README.md que contiene las instrucciones generales del laboratorio
- Archivo Dockerfile, que contiene las intrucciones de descarga para la Imágen de WordPress
- Archivo wp-config.php, que contiene los paramatros básicos para el despliegue de WordPress y su asociación a la Base de Datos MySQL en Amazon RDS
- Instrucciones para la instalación de AWS Tool (Necesario para los despliegues del contenedor)

No olvidar editar los parametros del archivo, tiene las referencias para un montaje básico de WordPress con la documentacion oficial del sitio :

 ```sh
https://wordpress.org/documentation/article/editing-wp-config-php/ 
```

## Creación y asignacion de recursos para la Instancia EC2

1. ingresa tu cuenta de AWS
2. Seleccionar la región de trabajo en EE.UU. Este (Norte de Virginia) us-east-1
3. Crear una instancia
- Asignar nombre a la instancia
- Seleccionar AMI de Amazon Linux 2023
- Selecccionar como tipo de instancia, t2.micro, sin editar
- Usar la clave de inicio de sesión por defecto, Vockey, para poder conectar por SSH a la instancia
- En la configuraciónde red, usar la VPC Predeterminada
- Solicitar la asignación de una IP Pública
- Crear un nuevo grupo de seguridad con un nuevo nombre, piensa en algo referencial para asociarlo a las instancias EC2
- En las reglas de entrada del grupo de seguridad, manten la de SSH para todo origen y agrega una segunda regla permitiendo el puerto 80 (HTTP) desde cualquier Origen.
- No editar las opciones de almacenamiento, con el espacio asignado por defecto es suficiente
- El resto de opciones se mantiene sin editar
- Finalizar con el lanzamiento de la Instancia

5. Cuando la instancia de EC2 este en ejecución, realizar la conexion por SSH para realizar la configuración
- Descargar la clave vockey en formato PPK desde los detalles de conexion de la cuenta AWS, se utilizará para conectarse a través de la aplicacion Putty
- Usando Putty, conecta a la instancia usando la IP Pública asignada a la Instaancia.
- Como usuario de conexión se utiliza el usuario de la instancia : ec2-user
- Con la conexion establecida, continuamos con las aplicaciones de Docker, Git y MariaDB para cargar herramientas de gestión de Base de Datos

7. Actualizar el sistema y sus componentes

```sh
sudo yum update -y
```

6. Instalar y habilitar Git en la Instancia

```sh
sudo yum install git -y
```

7. Instalar y habilitar permisos de ejecucioón privilegiados para Docker en la Instancia
```sh
sudo yum install docker
sudo usermod -a -G docker ec2-user
newgrp docker
```
8. Instalar y habilitar MariaDB en la Instancia
```sh
sudo yum install mariadb105-server.x86_64
```

Por el momento, pasemos al siguiente paso. Luego volveremos a la instancia para editar parametros de la BD de Amazon RDS


## Creacion de la Base de Datos RDS MySQL en AWS

NOTA: La creación de la base de datos demora, ten paciencia.

1. Ir a Amazon RDS
2. Hacer click en crear base de datos
3. Seleccionar Creación estándar
4. En tipo de motor, seleccionar MySQL
5. En plantillas, seleccionar Capa Gratuita
6. En configuracion, escribir el identificador de la BD con un nombre a elección
8. En credenciales elegir el nombre de usuario maestro (no se recomienda dejar el usuario por defecto)
9. Definir una contraseña maestra la base de datos
10. En Configuración de la instancia, mantener la opción por defecto
11. En Almacenamiento, mantener la opción por defecto.
12. Ahora en Conectividad, seleccionar "Conectarse a recurso informático EC2"
13. Seleccionar la instancia EC2 que creaste previamente.
14. En las opciones de VPN, usar tu segmentacion por defecto, debe compartir recursos con la Instancia EC2.
15. Como Grupo de Subredes, lo dejamos en "Configuración automática"
16. En Grupo de Seguridad VPC, creamos uno nuevo y damos un nombre referencial para asociarlo
17. En configuración adicional, mantener el puerto 3306
18. En Autenticación de bases de datos, seleccionar "Autenticación con contraseña"
19. Dar click al boton "Crear base de datos"

## Conectar a la instancia de base de datos, crear una nueva BD y editar permisos de usuarios

1. Volver a la conexión SSH hacia la instancia EC2
2. En el promt del usuario ec2-user, conectar a la BD usando la siguiente sintaxis

```sh
mysql -h punto_enlace_rds_mysql -P 3306 -u admin -p
```
Donde el punto de conexión corresponde al punto de enlace de la base de datos RDS MySQL

3. Crear la nueva base de datos
```sh
create database 'nuevo_nombre_bd';
```
4. Otorgar privilegios al usurio creado en la instancia MySQL, y cerrar la conexión remota a la BD.
```sh
GRANT ALL PRIVILEGES ON 'nuevo_nombre_bd'.* TO 'nombre_usuario';
quit
```

## Decargar el repositorio de GitHub y Compilar Imágen con Docker

1. En la ruta Raíz del usuario ec2-user crear un directorio de trabajo que contendra los repositorios locales, puedes usar cualquier nombre para tu referencia. Y luego ingresar en él.
   
```sh
sudo mkdir "nombredirectorio"
cd "nombredirectorio"
```
2. Usando los comandos de Git, clonar el repositorio en GitHub, y luego ingresar al nuevo directorio para editar los archivos base del laboratorio.

```sh
sudo git clone https://github.com/AlvaradoMiguel/miguel_lab_docker
cd miguel_lab_docker
```
3. En este punto ya puedes revisar los archivos "Dockerfile" y "wp-config.php", usando el editor te texto de linux "VIM" puedes editar los archivos.
- Debes editar el archivo wp-config.php para ingresar las variables de la base de datos y credenciales de acceso que usara WordPress para llegar a la base de datos MySQL de Amazon RDS
- El archivo Dockerfile tiene la configuración básica de funcionamiento para compilar la imagen de WordPress desde su repositorio original, la edición es de libre eleccion para personalizarlo.
  
```sh
sudo vim wp-config.php
```
4. Con los archivos Dockerfile y wp-config.php editados, procedemos a construir el contenedor usando Docker

```sh
sudo docker build -t 'nombre_imagen' .
```
Este proceso se conecta a los repositorios informados en la configuracion para descargar todo lo necesario para compilar los recursos que usará el contenedor.

5. Ejecutando el nuevo contenedor

Mediante la linea de comandos ingresamos los parametros necesarios para ejecutar el contenedor con un nombre local que se mostrará en nuestro sistema, y que hace referencia al nombre de la imagen recién compilada.
Además, en la sintaxis se pueden apreciar los puertos de red, locales y remotos, por los que se expondra el servicio de WordPress en la Instancia EC2. El puerto del lado de la instancia es el que se configuro dentro del grupo de seguridad de la instancia en AWS EC2.

```sh
sudo docker run -d -p 80:80 --name='nombre_local_contenedor 'nombre_imagen'
```
Con el siguiente comando puedes probar si la ejecución resulto exitosa, ya que enlistara los contenedores que estan en ejecución.

```sh
sudo docker ps
```
6. Probando el nuevo contenedor

Ahora, si la ejecución resultó existosa, nos conectaremos al servicio de WordPress que esta en el contenedor, accediendo a el mediante la IP Pública de la Instancia
Si carga la pagina de configuración inicial de WordPress, contianuamos !

```sh
Es la misma IP que se uso en la conexión SSH, pero ahora puesta en el navegador.
```

## Creando la Infraestructura de AWS para soportar y desplegar el contenedor

1. Crear repositorio ECR (Amazon Elastic Container Registry)
- Ingresar en buscador de AWS al Servicio ECR, y luego presionar el boton "Comenzar"
- En las opciones solo editar 2 de ellas, la de mantener la exposición del contenido de manera privada, y la de asignar un nombre al recurso.
- Presionar el boton "Crear Repositorio"

3. Configurar permisos en la instancia para conectarse al repositorio de AWS ECR
   
En la raiz del usuario ec2-user crear el siguiente directorio

```sh
sudo mkdir ~/.aws/
```
- Luego crear el archivo de credenciales usando la llave AWS CLI que se obtiene desde los detalles de conexioón de la consola AWS

```sh
sudo vim ~/.aws/credentials
  ```

- Dentro de este archivo se copia la llave de acceso, correspondientes al "aws_access_key_id" y al "aws_secret_access_key"
- Ahora se deben seguir las instrucciones de "Comandos de envío" que tiene el repositorio de ECR, esta opción esta entre los botenes de acción del repositorio.
- Ingresar al directorio que Git sincronizo localmente en la instancia y continuar con los siguiente comandos. (Estos comandos incluyen valores del montaje de pruebas, debes obtener tus propias sintaxs en base a tu propia configuración)

```sh
aws ecr get-login-password --region us-east-1 | docker login --username AWS --password-stdin 455604117431.dkr.ecr.us-east-1.amazonaws.com

docker build -t repo_docker_wp_mah .

docker tag repo_docker_wp_mah:latest 455604117431.dkr.ecr.us-east-1.amazonaws.com/repo_docker_wp_mah:latest

docker push 455604117431.dkr.ecr.us-east-1.amazonaws.com/repo_docker_wp_mah:latest
  ```

3. Crear el balanceador de carga y sus objetivos (target)

- Ahora, en el menú lateral, buscar el acceso a los Balanceadores de carga
- Presionar el boton "Create load balancer"
- Que sea del tipo ALB (Application Load Balancer), y presionar el botón "Crear"
- Asignar un nombre al balanceador
- En Network Mapping confirmar la VPC y seleccionar todas las subredes asociadas a las Instancias EC2
- En los Grupos de Seguridad, seleccionar todos los grupos asociados a las Intancias EC2 y RDS que esten asociadas al ejercicio. Desmarcando el grupo "Default"
- En Listeners and routing crear los "Target Group" para el balanceador (Se abrirá una nueva ventana, no cerrar la anterior ya que volveremos)
- Que sea del tipo Instancias
- Ingresar un nombre para el "Target Group"
- Indicar el protocolo HTTP con el puerto 80
- Verificar que la VPC corresponda a la del resto de los recursos
- Presionar el boton "Siguiente"
- Seleccionar las instancias que participar del grupo de objetivos del balanceador
- Presionar el boton "Include as pendin below"
- Presionar el boton "Create Target Group"
- Volver a la ventana de creación del balanceador, refrescar las opciones del Listener y seleccionar el grupo de objetivos que recien creamos
- Y presionar el boton "Create Load Balancer"
- Cuando el Balanceador este listo, volvemos a las opciones del balanceador para eliminar el listener vigente que creamos anteriormente, y dejar que en las etapas posteriores se vuelva a crear. Se observa un error con esta cuenta de AWS para Estudiantes que obliga a realizar este paso.
  

3. Crear una defición de tareas para el Cluster
    - Presionar el boton "Crear una nueva definición de tarea"
    - Asignar un nombre descriptivo a la familia de definición de tareas
    - Especifique el nombre del contenedor que desplegará y la dirección URI del repositorio previamente creado
    - Confirmar el Mapeo de Puerto al puerto 80 y presionamos el boton "Siguiente"
    - En Entorno, sección Entorno de la aplicación, seleccionamos o mantenemos la opición AWS Fargate
    - Sistema operativo/arquitectura Linux
    - Tamaño de la tarea: 2 vCPU y 4 GB de memoria
    - En Rol de tarea elegimos un rol con permisos dentro de los recursos, en este caso usaremos el de nombre "LabRole"
    - En Rol de ejecución de tareas, tambien seleccionamos a "LabRole"
    - Almacenamiento efímero lo dejamos por defecto en 21GB
    - Las siguiente opciones se mantienen sin modificaciones y presionamos el boton "Siguiente"
    - Validamos la configuracion mediante el resumen y presionamos el boton "Crear"


4. Crear el Cluster de ECS (Elastic Container Services) con funciones FareGate
    - Asignar un nombre al cluster
    - Verificar que este asociado a la red VPC de la instancia EC2 y RDS, y que contenga las subredes asociadas a ellos
    - En el apartado de Infraestructura, verificar que este marcada la opción AWS Fargate (sin servidor)
    - Presionar el boton "Crear"


5. Crear una defición de Servicios para el Cluster
    - Dentro de las opciones del Cluster recien creado, vamos a la pestaña "Servicios" y presionamos el boton "Crear"
    - En Configuración informática seleccionamos "Estrategia de proveedor de capacidad"
    - En Estrategia de proveedor de capacidad, selecionamos FARGTE
    - Mantenems la Versión de la plataforma en "LATEST"
    - En Configuración de implementación, seleccionamos Servicio
    - Dentro de la Definición de tarea, en el campo "Familia", seleccionar nuestra tarea creada previamente, junto a la opción de revisón mas reciente.
    - Asignamos un nombre identificatorio al servicio
    - Mantenemos el modo Réplica con una sola Tarea deseada
    - En el apartador de Redes confirmar que la VPN corresponda al resto de los recursos al igual que las subredes en ella
    - En los grupos de seguridad, marcar todos los grupos involucrados en la arquitectura del EC2 y RDS. Y desmarcamos el grupo "Default"
    - En la sección de Balanceo de carga, seleccionamos el tipo de "Balanceador de carga de aplicaciones"
    - Utilizamos el balanceador existente que creamos pasos atrás, el que también trae el contenedor asociado a él.
    - Crear un nuevo agente de escucha para el puerto 80 http
    - Crear un nuevo grupo de destino y asignar un nombre descriptivo
    - Confirmar la ruta de comprobación de estado " / " 
    - Ahora presionamos el boton "Crear"

## Revisar el estado de la implementación a través de CloudFormation y pruebas finales

1. En el buscador de recursos de AWS buscar el de CloudFormation, en él nos aparece el progreso de la creación de la Infraestructura. Cuando este completado, vamos otra vez al balaceador de carga en la sección de administración de recursos EC2
2. Entre los Balanceadores de carga existentes buscamos el que corresponde a nuestro WordPress y copiamos la dirección DNS.
3. Pegamos esta dirección en el navegador y debería traernos el portal de configuración de WordPress en su primera ejecución.
4. Si te permite configurar los parametros iniciale de WordPress, el laboratorio resulto sin problemas

## Miguel Alvarado - mi.alvaradoh@duocuc.cl


   
   
