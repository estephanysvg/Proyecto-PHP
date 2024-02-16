<?php

/*
 * Acceso a datos con BD Usuarios : 
 * Usando la librería PDO *******************
 * Uso el Patrón Singleton :Un único objeto para la clase
 * Constructor privado, y métodos estáticos 
 */
class AccesoDatos {
    
    private static $modelo = null;
    private $dbh = null;
    
    public static function getModelo(){
        if (self::$modelo == null){
            self::$modelo = new AccesoDatos();
        }
        return self::$modelo;
    }
    
   // Constructor privado  Patron singleton
   
    private function __construct(){
        try {
            $dsn = "mysql:host=".DB_SERVER.";dbname=".DATABASE.";charset=utf8";
            $this->dbh = new PDO($dsn,DB_USER,DB_PASSWD);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e){
            echo "Error de conexión ".$e->getMessage();
            exit();
        }  

    }

    // Cierro la conexión anulando todos los objectos relacioanado con la conexión PDO (stmt)
    public static function closeModelo(){
        if (self::$modelo != null){
            $obj = self::$modelo;
            // Cierro la base de datos
            $obj->dbh = null;
            self::$modelo = null; // Borro el objeto.
        }
    }


    // Devuelvo cuantos filas tiene la tabla

    public function numClientes ():int {
      $result = $this->dbh->query("SELECT id FROM Clientes");
      $num = $result->rowCount();  
      return $num;
    } 
    

    // SELECT Devuelvo la lista de Usuarios
    public function getClientes($primero, $cuantos, $campoAordenar, $metodoOrdenacion): array
    {
        $tuser = [];
        
        // Defino la dirección de ordenación para usar en la sentencia SQL
        $orden = ($metodoOrdenacion == "ASC") ? "DESC" : "ASC";
    
        // Crea la sentencia preparada con el uso de bindParam para evitar SQL injection
        $stmt_usuarios = $this->dbh->prepare("SELECT * FROM Clientes ORDER BY $campoAordenar $orden LIMIT :primero, :cuantos");
        
        // Bind de parámetros
        $stmt_usuarios->bindParam(':primero', $primero, PDO::PARAM_INT);
        $stmt_usuarios->bindParam(':cuantos', $cuantos, PDO::PARAM_INT);
        
        // Si falla termina el programa
        if (!$stmt_usuarios->execute()) {
            die(__FILE__ . ':' . __LINE__ . $stmt_usuarios->errorInfo()[2]);
        }
    
        // Configuro el modo de obtención de resultados como objetos de la clase Cliente
        $stmt_usuarios->setFetchMode(PDO::FETCH_CLASS, 'Cliente');
    
        // Obtengo los resultados
        while ($user = $stmt_usuarios->fetch()) {
            $tuser[] = $user;
        }
    
        // Devuelvo el array de objetos
        return $tuser;
    }
      
    // SELECT Devuelvo un usuario o false
    public function getCliente(int $id)
    {
        $cli = false;
        $stmt_cli = $this->dbh->prepare("SELECT * FROM Clientes WHERE id = :id");
        $stmt_cli->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt_cli->setFetchMode(PDO::FETCH_CLASS, 'Cliente');
    
        if ($stmt_cli->execute()) {
            $obj = $stmt_cli->fetch();
            if ($obj !== false) {
                $cli = $obj;
            }
        }
    
        return $cli;
    }
    

     
    public function getClienteSiguiente($id)
    {
        $cli = false;
        $stmt_cli = $this->dbh->prepare("SELECT * FROM Clientes WHERE id > ? LIMIT 1");
        $stmt_cli->bindParam(1, $id, PDO::PARAM_INT);
        $stmt_cli->setFetchMode(PDO::FETCH_CLASS, 'Cliente');
    
        if ($stmt_cli->execute()) {
            $obj = $stmt_cli->fetch();
            if ($obj !== false) {
                $cli = $obj;
            }
        }
    
        return $cli;
    }
    
    

    public function getClienteAnterior($id)
{
    $cli = false;
    $stmt_cli = $this->dbh->prepare("SELECT * FROM Clientes WHERE id < ? ORDER BY id DESC LIMIT 1");
    $stmt_cli->bindParam(1, $id, PDO::PARAM_INT);
    $stmt_cli->setFetchMode(PDO::FETCH_CLASS, 'Cliente');

    if ($stmt_cli->execute()) {
        $obj = $stmt_cli->fetch();
        if ($obj !== false) {
            $cli = $obj;
        }
    }

    return $cli;
}




public function modCliente($cli): bool
{
    $stmt_moduser = $this->dbh->prepare("UPDATE Clientes SET first_name = :first_name, last_name = :last_name, email = :email, gender = :gender, ip_address = :ip_address, telefono = :telefono WHERE id = :id");
    $stmt_moduser->bindParam(':first_name', $cli->first_name);
    $stmt_moduser->bindParam(':last_name', $cli->last_name);
    $stmt_moduser->bindParam(':email', $cli->email);
    $stmt_moduser->bindParam(':gender', $cli->gender);
    $stmt_moduser->bindParam(':ip_address', $cli->ip_address);
    $stmt_moduser->bindParam(':telefono', $cli->telefono);
    $stmt_moduser->bindParam(':id', $cli->id, PDO::PARAM_INT);

    $stmt_moduser->execute();
    $resu = ($stmt_moduser->rowCount() == 1);
    return $resu;
}
public function getIdCliente($email)
{
    $stmt = $this->dbh->prepare("SELECT id FROM Clientes WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        return $result['id'];
    } else {
        return null; // o cualquier valor que elijas para indicar que no se encontró el cliente
    }
}

  
public function addCliente($cli): bool
{
    $stmt_crearcli = $this->dbh->prepare("INSERT INTO `Clientes`(`first_name`, `last_name`, `email`, `gender`, `ip_address`, `telefono`) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt_crearcli->bindParam(1, $cli->first_name);
    $stmt_crearcli->bindParam(2, $cli->last_name);
    $stmt_crearcli->bindParam(3, $cli->email);
    $stmt_crearcli->bindParam(4, $cli->gender);
    $stmt_crearcli->bindParam(5, $cli->ip_address);
    $stmt_crearcli->bindParam(6, $cli->telefono);

    $stmt_crearcli->execute();
    $resu = ($stmt_crearcli->rowCount() == 1);
    return $resu;
}


   
    //DELETE 
    public function borrarCliente(int $id): bool
    {
        $stmt_boruser = $this->dbh->prepare("DELETE FROM Clientes WHERE id = :id");
        $stmt_boruser->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt_boruser->execute();
    
        // Verificar si se eliminó un registro
        $resu = ($stmt_boruser->rowCount() == 1);
    
        return $resu;
    }
    
    function getClienteEmail($email)
    {
        $cli = false;
    
        $stmt_usuario = $this->dbh->prepare("SELECT * FROM Clientes WHERE email = :email");
        if ($stmt_usuario == false) die(print_r($this->dbh->errorInfo(), true));
    
        // Enlazar el marcador de posición :email con el valor de $email
        $stmt_usuario->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt_usuario->execute();
    
        $result = $stmt_usuario->fetch(PDO::FETCH_OBJ);
        if ($result) {
            $cli = $result;
        }
    
        return $cli;
    }
    

    // Devuelve el último id insertado en la Base de Datos.

    function getUltimoId()
    {
        $cli = false;
    
        $stmt_usuario = $this->dbh->prepare("SELECT AUTO_INCREMENT AS id FROM information_schema.TABLES WHERE TABLE_SCHEMA='cliente' AND TABLE_NAME='clientes'");
        
        if ($stmt_usuario) {
            $stmt_usuario->execute();
    
            // Fetching the result
            $result = $stmt_usuario->fetch(PDO::FETCH_OBJ);
            
            if ($result) {
                $cli = $result->id;
            }
        } else {
            // Handle the error if prepare fails
            die("Error in query preparation: " . implode(" ", $this->dbh->errorInfo()));
        }
    
        return $cli;
    }
    
    

    function getUser($login, $password)
{
    $user = false;

    // Obtener el hash almacenado en la base de datos para el usuario proporcionado
    $stmt_usuario = $this->dbh->prepare("SELECT * FROM users WHERE login = :login AND password = :password");
    $stmt_usuario->bindParam(':login', $login, PDO::PARAM_STR);
    
    // Utilizar MD5 para la contraseña
    $hashedPassword = md5($password);
    $stmt_usuario->bindParam(':password', $hashedPassword, PDO::PARAM_STR);

    $stmt_usuario->execute();

    $result = $stmt_usuario->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $user = true;
    }

    return $user;
}

function getRol($login)
{
    // Devolver solo el rol del usuario
    $rol = false;

    $stmt_usuario = $this->dbh->prepare("SELECT rol FROM users WHERE login = :login");
    $stmt_usuario->bindParam(':login', $login, PDO::PARAM_STR);
    $stmt_usuario->execute();

    $result = $stmt_usuario->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $rol = $result['rol'];
    }

    return $rol;
}




    
     // Evito que se pueda clonar el objeto. (SINGLETON)
    public function __clone()
    { 
        trigger_error('La clonación no permitida', E_USER_ERROR); 
    }

    
}



