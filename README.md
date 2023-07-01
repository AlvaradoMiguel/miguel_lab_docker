# Laboratorio Tecnologías de Virtualización - Wordpress en contenedor y aplicación en AWS ECS

![N|Solid](https://upload.wikimedia.org/wikipedia/commons/thumb/a/aa/Logo_DuocUC.svg/2560px-Logo_DuocUC.svg.png)

Con este laboratorio aprederás a contruir una imagen de contenedor utilizando una Instancia de AWS como medio de trabajo, a esta instancia se le provisionan tanto del entorno de Docker y Git para gestionar la imagen, y luego propagarla hacia el sistema AWS ECR, que se encargara de gestionar el contenedor y levantar el entorno que desplegará el contenido paquetizado en el contenedor. Para finalmente, montar esta infraestructura detras de un balanceador de carga que conectara el contenedor y una base de datos para levantar la aplicación de WordPress.

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


## Paso de creación de Infraestructura en Amazos AWS

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

4. Cuando la instancia de EC2 este en ejecución, realizar la conexion por SSH para realizar la configuración
    - Descargar la clave vockey en formato PPK desde los detalles de conexion de la cuenta AWS, se utilizará para conectarse a través de la aplicacion Putty
    - Usando Putty, conecta a la instancia usando la IP Pública asignada a la Instaancia.
    - Como usuario de conexión se utiliza el usuario de la instancia : ec2-user
    - Con la conexion establecida, continuamos con las aplicaciones de Docker, Git y MariaDB para cargar herramientas de gestión de Base de Datos

5. Actualizar el sistema y sus componentes

```sh
sudo yum update -y
```

6. Instalar y habilitar Git en la Instancia

```sh
sudo yum install git -y
```

7. Instalar y habilitar Docker en la Instancia
```sh
sudo yum install docker
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
  
