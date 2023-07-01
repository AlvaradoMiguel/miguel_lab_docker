# Laboratorio Tecnologías de Virtualización - Wordpress en contenedor y aplicación en AWS ECS

![N|Solid](https://upload.wikimedia.org/wikipedia/commons/thumb/a/aa/Logo_DuocUC.svg/2560px-Logo_DuocUC.svg.png)

Con este laboratorio aprederás y podras implementar un contenedor con imagen de WordPress y administrador por la plataforma de gestión de contenedores de ECS de Amazon ECS.
Dentro de la infraestructura usada en AWS, segun los requerimiento del laboratorio, se debe considerar lo siguiente:

- Instancias EC2
- Dentro de la Instancia EC2 se debe instalar herramientas Docker y Git
- Grupos de Seguridad
- VPC
- Application Load Balancer
- ECS
- ECR
- Bases de datos RDS - En este caso, Aurora MySQL


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
      
## Creacion de la Base de Datos RDS AURORA en AWS

NOTA: La creación de la base de datos demora, ten paciencia.

1. Ir a Amazon RDS
2. Hacer click en crear base de datos
3. Seleccionar Creación estándar
4. en tipo de motor seleccionar Aurora(MySQL Compatible)
5. En plantillas elegir desarrollo y pruebas
6. Identificador del clúster de base de datos debemos escribir un nombre a eleccion 
7. En credenciales elegir el nombre de usuario maestro (como es test dejaremos admin)

8. Contraseña maestra elegir una  debe tener al menos 8 caracteres(esto se utilizara para conectarnos a la bd luego)

9. En Cluster storage configuration elegir Aurora Standard (esto es para los costos bajos)

10. En Configuración de la instancia elegir Clases con ráfagas e elegir la que consideremos apropiada la small es una bd con 2GB de memoria algo para un sitio estandar con no tanto requisito o flujo de usuario

11. En Disponibilidad y durabilidad si deseo crear algo resilente a fallos elegir crear nodo si no es el caso y es solo test elegir No crear una réplica de Aurora

12. En Conectividad elegir Conectarse a un recurso informático de EC2 esto es para establecer una conexion interna con nuestra instancia ec2 con el contenedor y crear los Security Group de conexion de RDS (todas las demas opciones dejar por defecto)

13. En Autenticación de bases de datos elegir Autenticación con contraseña

14. dejar todo lo demas por defecto y hacer click en crear base de datos

1. Actualizar el sistema y sus componentes
```sh
sudo yum update -y
```
2. Instalar Git en la Instancia
```sh
sudo yum install git
```
3. Instalar Docker en la Instancia
```sh
sudo yum install git
```
   
