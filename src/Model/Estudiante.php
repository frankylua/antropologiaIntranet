<?php
declare(strict_types=1);
namespace App\Model;

class Estudiante extends Usuario{
    public function __construct(){
    }
    //insertar($profesor,$tipo_est,$fech_ing,$fecha_grad,$promedio,$trabaja,$orientacion,$sit_ocup
    public function insertar($profesor,$tipo_est,$fech_ingr,$fech_grad,$promedio,$trabaja,$orientacion,$sit_ocup,$usuario){
        return $this->escritura(
            'INSERT INTO estudiante (prof_guia,tipo_est,fech_ingr,fecha_egr,prom_notas,trabaja,orient_disc,sit_previa,usuario) '
            . 'VALUES (:guia,:tipo,:ingreso,:egreso,:promedio,:trabaja,:orientacion,:situacion,:usuario)',
            ['guia'=>$profesor,'tipo'=>$tipo_est,'ingreso'=>$fech_ingr,'egreso'=>$fech_grad ?: null,
                'promedio'=>$promedio,'trabaja'=>$trabaja,'orientacion'=>$orientacion,'situacion'=>$sit_ocup,'usuario'=>$usuario]
        );
        //return true;
       
    }



        
    // public function editar($id_curso,$nom_curso,$creditos,$caracter,$periodo,$anio_curso,$programa,$car_hor){
    //     $sql="UPDATE curso SET creditos='$creditos',caracter='$caracter',periodo='$periodo',anio_curso='$anio_curso',carga_hor='$car_hor',nom_curso='$nom_curso' where id_curso='$id_curso'";
    //     return ejecutarConsulta($sql);
        
    // }
    // public function mostrarPorId($id_curso){
    //     $sql="SELECT * FROM curso c JOIN nombre_curso n ON c.nom_curso=n.id_nom_curso  WHERE c.id_curso='$id_curso'";
    //     return resultadoConsultaPorId($sql);
    // }
        
    // public function mostrar(){
         
    //     $sql="SELECT c.id_curso,n.nom_curso,c.periodo,c.anio_curso FROM curso c JOIN nombre_curso n ON c.nom_curso=n.id_nom_curso  ORDER BY c.anio_curso DESC";
    //     return ejecutarConsultaResultados($sql);
        
    // }

    public function mostrarTipo(){
        $consulta = conexion()->prepare('SELECT id_tipo_est,tipo FROM tipo_estudiante ORDER BY tipo');
        $consulta->execute();
        // Conserva los campos nominales y el contrato numérico de ajaxSelect heredado.
        return $consulta->fetchAll(\PDO::FETCH_BOTH);
    }
    public function mostrarEst(){    
        return $this->listarEstudiantes();
    }

    public function mostrarEstTipo($tipo){    
        return $this->listarEstudiantes((int) $tipo);
    }
    public function mostrarEstId($id_usu){
        $consulta = conexion()->prepare(
            'SELECT u.id_usuario,u.pais_res,u.pais_nac,u.login,u.fecha_nac,'
            . 'u.nombres,u.ap_mat,u.ap_pat,u.tipo_doc,u.nro_doc,u.inst_usuario,'
            . 'u.genero,u.region,u.comuna,u.telefono,u.direccion,u.cont_em,u.tel_em,'
            . 'l.id_login,l.correo,e.id_est,e.prof_guia,e.tipo_est,e.fech_ingr,'
            . 'e.fecha_egr,e.prom_notas,e.trabaja,e.orient_disc,e.sit_previa,e.usuario,'
            . 'i.id_inst,i.inst,pn.pais AS p_nac,pr.pais AS p_resi,pu.id_pueblo,pu.pueblo,'
            . 'te.id_tipo_est,te.tipo,lu.id_linea_usuario,lu.linea_inv,li.id_linea_inv,li.linea '
            . 'FROM usuario u JOIN login l ON u.login=l.id_login '
            . 'JOIN estudiante e ON u.id_usuario=e.usuario '
            . 'JOIN institucion i ON u.inst_usuario=i.id_inst '
            . 'JOIN pais pn ON u.pais_nac=pn.id_pais JOIN pais pr ON u.pais_res=pr.id_pais '
            . 'JOIN pueblo pu ON u.pueblo=pu.id_pueblo '
            . 'JOIN tipo_estudiante te ON e.tipo_est=te.id_tipo_est '
            . 'JOIN linea_usuario lu ON u.id_usuario=lu.usuario '
            . 'JOIN linea_investigacion li ON li.id_linea_inv=lu.linea_inv '
            . 'WHERE u.id_usuario=:id_usuario ORDER BY u.nombres'
        );
        $consulta->execute(['id_usuario' => $id_usu]);
        return $consulta->fetchAll(\PDO::FETCH_ASSOC);
    }

    // mostrar respuesta de la busqueda profesores 
    public function mostrarProf($busqueda){    
        $texto = explode(" ", $busqueda);
        $esp= count($texto) - 1;// codigo para validar apellidos --- pendiente ----
        $sql="SELECT u.id_usuario,u.nombres,u.ap_pat,u.ap_mat FROM usuario u JOIN profesor p ON u.id_usuario=p.usuario WHERE u.nombres LIKE '".$busqueda."%'";
        return ejecutarConsultaResultados($sql);
    }

    public function mostrarNomProf(int $idEstudiante){
        $consulta = conexion()->prepare(
            'SELECT guia.id_usuario, guia.nombres, guia.ap_pat, guia.ap_mat '
            . 'FROM estudiante e '
            . 'JOIN usuario guia ON guia.id_usuario = e.prof_guia '
            . 'JOIN profesor p ON p.usuario = guia.id_usuario '
            . 'WHERE e.usuario = :id_estudiante'
        );
        $consulta->execute(['id_estudiante' => $idEstudiante]);
        return $consulta->fetchAll();
    }


    public function mostrarEstFiltro($busqueda){    
        return $this->listarEstudiantes(0, (string) $busqueda);
            
    }
    public function mostrarDatosProgEst($id_usu){
        $consulta = conexion()->prepare(
            'SELECT e.prof_guia,e.tipo_est,e.fech_ingr,e.fecha_egr,e.prom_notas,e.trabaja,e.orient_disc,e.sit_previa,'
            . 'u.id_usuario,lu.linea_inv,l.id_login,u.inst_usuario FROM usuario u '
            . 'JOIN estudiante e ON e.usuario=u.id_usuario JOIN login l ON l.id_login=u.login '
            . 'JOIN institucion i ON i.id_inst=u.inst_usuario LEFT JOIN linea_usuario lu ON lu.usuario=u.id_usuario '
            . 'WHERE u.id_usuario=:usuario ORDER BY lu.linea_inv'
        );
        $consulta->execute(['usuario'=>$id_usu]);
        return $consulta->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function editarDatProg($id_usu, $profesor,$fech_ing, $fech_grad, $promedio,$trabaja, $orientacion, $sit_ocup){
        return $this->escritura(
            'UPDATE estudiante SET prof_guia=:guia,fech_ingr=:ingreso,fecha_egr=:egreso,prom_notas=:promedio,'
            . 'trabaja=:trabaja,orient_disc=:orientacion,sit_previa=:situacion WHERE usuario=:usuario',
            ['guia'=>$profesor,'ingreso'=>$fech_ing,'egreso'=>$fech_grad ?: null,'promedio'=>$promedio,
                'trabaja'=>$trabaja,'orientacion'=>$orientacion,'situacion'=>$sit_ocup,'usuario'=>$id_usu]
        );
    }

    public function editarTipoEst($id_usu,$tipo_est){
        if ((int)$tipo_est===6) \App\Security\AcademicSession::requireMigration(conexion());
        return $this->escritura('UPDATE estudiante SET tipo_est=:tipo WHERE usuario=:usuario',
            ['tipo'=>$tipo_est,'usuario'=>$id_usu]);
    }

    private function escritura(string $sql, array $parametros): \PDOStatement {
        $consulta = conexion()->prepare($sql);
        if (!$consulta->execute($parametros)) {
            throw new \RuntimeException('ESCRITURA_NO_CONFIRMADA');
        }
        return $consulta;
    }

    public function listarEstudiantes(int $tipo = 0, string $busqueda = ''): array {
        $sql = 'SELECT u.id_usuario,u.nombres,u.ap_pat,u.ap_mat,t.tipo,t.id_tipo_est AS id_tipo,l.id_login '
            . 'FROM usuario u JOIN login l ON l.id_login=u.login JOIN estudiante e ON e.usuario=u.id_usuario '
            . 'JOIN tipo_estudiante t ON t.id_tipo_est=e.tipo_est WHERE 1=1';
        $parametros = [];
        if ($tipo !== 6 && \App\Security\AcademicSession::migrationReady(conexion())) $sql .= ' AND e.tipo_est<>6';
        if ($tipo !== 0) { $sql .= ' AND e.tipo_est=:tipo'; $parametros['tipo'] = $tipo; }
        if ($busqueda !== '') {
            $sql .= " AND CONCAT_WS(' ',u.nombres,u.ap_pat,u.ap_mat) LIKE :busqueda";
            $parametros['busqueda'] = '%' . $busqueda . '%';
        }
        $consulta = conexion()->prepare($sql . ' ORDER BY u.nombres,u.ap_pat,u.id_usuario');
        $consulta->execute($parametros);
        return $consulta->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** Lecturas actuales con bloqueo para escrituras; ninguna selecciona credenciales. */
    public function resolverCuentaEstudiante(int $login, bool $bloquear = false): array {
        $sufijo = $bloquear ? ' FOR UPDATE' : '';
        $q = conexion()->prepare('SELECT id_login FROM login WHERE id_login=:login' . $sufijo);
        $q->execute(['login'=>$login]);
        if ($q->fetchColumn() === false) throw new \DomainException('ESTUDIANTE_NO_ENCONTRADO', 404);
        $q = conexion()->prepare('SELECT id_usuario,login,inst_usuario FROM usuario WHERE login=:login' . $sufijo);
        $q->execute(['login'=>$login]);
        $usuarios = $q->fetchAll(\PDO::FETCH_ASSOC);
        if (count($usuarios) !== 1) throw new \DomainException('IDENTIDAD_INCOMPATIBLE', 409);
        $q = conexion()->prepare('SELECT id_est,usuario,tipo_est,prof_guia FROM estudiante WHERE usuario=:usuario' . $sufijo);
        $q->execute(['usuario'=>$usuarios[0]['id_usuario']]);
        $estudiantes = $q->fetchAll(\PDO::FETCH_ASSOC);
        if (count($estudiantes) !== 1 || !in_array((int)$estudiantes[0]['tipo_est'], [1,2,3,4,5,6,7], true)) {
            throw new \DomainException('IDENTIDAD_INCOMPATIBLE', 409);
        }
        return $usuarios[0] + $estudiantes[0];
    }

    public function resolverEstudiante(int $usuario, bool $bloquear = false): array {
        $q = conexion()->prepare('SELECT login FROM usuario WHERE id_usuario=:usuario');
        $q->execute(['usuario'=>$usuario]);
        $login = $q->fetchColumn();
        if ($login === false) throw new \DomainException('ESTUDIANTE_NO_ENCONTRADO', 404);
        $objetivo = $this->resolverCuentaEstudiante((int)$login, $bloquear);
        if ((int)$objetivo['id_usuario'] !== $usuario) throw new \DomainException('IDENTIDAD_INCOMPATIBLE', 409);
        return $objetivo;
    }

    public function validarReferenciasPrograma(array $datos, ?int $guiaActual = null): void {
        $q = conexion()->prepare('SELECT estado_profesor FROM profesor WHERE usuario=:guia FOR UPDATE');
        $q->execute(['guia'=>$datos['profesor']]);
        $guias = $q->fetchAll(\PDO::FETCH_COLUMN);
        if (count($guias) !== 1 || ($datos['profesor'] !== $guiaActual && (int)$guias[0] !== 2)) {
            throw new \DomainException('GUIA_NO_ELEGIBLE', 422);
        }
        $marcas = implode(',', array_fill(0, count($datos['lineaInv']), '?'));
        $q = conexion()->prepare('SELECT id_linea_inv FROM linea_investigacion WHERE id_linea_inv IN (' . $marcas . ') FOR UPDATE');
        $q->execute($datos['lineaInv']);
        if (count($q->fetchAll(\PDO::FETCH_COLUMN)) !== count($datos['lineaInv'])) {
            throw new \DomainException('LINEAS_INVALIDAS', 422);
        }
    }

    public function guardarLineas(int $usuario, array $lineas): void {
        $q = conexion()->prepare('SELECT id_linea_usuario,linea_inv FROM linea_usuario WHERE usuario=:usuario FOR UPDATE');
        $q->execute(['usuario'=>$usuario]);
        $actuales = $q->fetchAll(\PDO::FETCH_ASSOC);
        $conservar = [];
        foreach ($actuales as $linea) {
            $id = (int)$linea['linea_inv'];
            if (!in_array($id, $lineas, true) || isset($conservar[$id])) {
                $this->escritura('DELETE FROM linea_usuario WHERE id_linea_usuario=:id AND usuario=:usuario',
                    ['id'=>$linea['id_linea_usuario'],'usuario'=>$usuario]);
            } else { $conservar[$id] = true; }
        }
        foreach ($lineas as $linea) {
            if (!isset($conservar[$linea])) {
                $this->escritura('INSERT INTO linea_usuario (usuario,linea_inv) VALUES (:usuario,:linea)',
                    ['usuario'=>$usuario,'linea'=>$linea]);
            }
        }
    }

    public function guardarPrograma(int $usuario, array $datos, int $institucion): void {
        $this->editarDatProg($usuario,$datos['profesor'],$datos['fech_ing'],$datos['fech_grad'],
            $datos['promedio'],$datos['trabaja'],$datos['orientacion'],$datos['sit_ocup']);
        $this->escritura('UPDATE usuario SET inst_usuario=:institucion WHERE id_usuario=:usuario',
            ['institucion'=>$institucion,'usuario'=>$usuario]);
        $this->guardarLineas($usuario,$datos['lineaInv']);
    }

    /** La transacción pertenece al endpoint, incluida una institución nueva. */
    public function crearRegistro(array $persona, array $programa, int $tipo, array $permisos, string $pass): int {
        $pdo = conexion();
        if (!$pdo->inTransaction()) throw new \RuntimeException('TRANSACCION_REQUERIDA');
        $institucion = (new Institucion())->resolverId($programa['inst'],$programa['nueva_institucion']);
        $this->escritura('INSERT INTO login (correo,pass) VALUES (:correo,:pass)', ['correo'=>$persona['correo'],'pass'=>$pass]);
        $login = (int)$pdo->lastInsertId();
        if ($login < 1) throw new \RuntimeException('LOGIN_NO_CONFIRMADO');
        foreach ($permisos as $permiso) {
            $this->escritura('INSERT INTO permiso_login (id_login,id_permiso) VALUES (:login,:permiso)',
                ['login'=>$login,'permiso'=>$permiso]);
        }
        $this->escritura('INSERT INTO usuario (login,inst_usuario,pueblo,pais_res,pais_nac,fecha_nac,nombres,ap_mat,ap_pat,'
            . 'tipo_doc,nro_doc,genero,region,comuna,telefono,direccion,cont_em,tel_em) '
            . 'VALUES (:login,:institucion,:pueblo,:pais_res,:pais_nac,:fecha_nac,:nombres,:ap_mat,:ap_pat,'
            . ':tipo_doc,:nro_doc,:genero,:region,:comuna,:telefono,:direccion,:cont_em,:tel_em)',
            ['login'=>$login,'institucion'=>$institucion] + array_intersect_key($persona, array_flip([
                'pueblo','pais_res','pais_nac','fecha_nac','nombres','ap_mat','ap_pat','tipo_doc','nro_doc',
                'genero','region','comuna','telefono','direccion','cont_em','tel_em'])));
        $usuario = (int)$pdo->lastInsertId();
        if ($usuario < 1) throw new \RuntimeException('USUARIO_NO_CONFIRMADO');
        $this->insertar($programa['profesor'],$tipo,$programa['fech_ing'],$programa['fech_grad'],$programa['promedio'],
            $programa['trabaja'],$programa['orientacion'],$programa['sit_ocup'],$usuario);
        $this->guardarLineas($usuario,$programa['lineaInv']);
        return $usuario;
    }

    public function inactivarRegistro(int $loginObjetivo, int $loginActor): void {
        if (!conexion()->inTransaction()) throw new \RuntimeException('TRANSACCION_REQUERIDA');
        if ($loginObjetivo===$loginActor) throw new \DomainException('ELIMINACION_PROPIA_NO_AUTORIZADA',403);
        \App\Security\AcademicSession::requireMigration(conexion());
        $objetivo=$this->resolverCuentaEstudiante($loginObjetivo,true);
        $this->editarTipoEst((int)$objetivo['id_usuario'],6);
    }
}
