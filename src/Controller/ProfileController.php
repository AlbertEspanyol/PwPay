<?php

namespace ProjWeb2\PRACTICA\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class ProfileController
{
    private ContainerInterface $container;
    //Variable que 'sinicialitza aquí per a poder fer-la servir a totes les funcions
    private string $errors;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->errors = '';
    }

    public function submitData(Request $request, Response $response): Response{
        //S'agafa la id del user
        $id = $_SESSION['user_id'];

        //Si hi ha algun fitxer penjat procedeix
        if(!empty($_FILES['avatar']['name'])) {

            //Es declara la carpeta on aniran a parar els fitxers
            $target_dir = "uploads/";

            //S'agafa l'extensio del fitxer
            $ext = explode('/', $_FILES['avatar']['type']);

            //S'agafa la localitzacio temporal del fitxer
            $tmp = $_FILES['avatar']['tmp_name'];

            //Es crea un nou nom unic pel fitxer basat amb el que l'usuari utilitzava anteriorment
            $path = $this->container->get('user_repository')->getInfoById('pfp_path', $id);
            if($path != 'Unknown'){
                $num = intval($path[0]) + 1;
                $newname = $num . "_" . $id . "_pfp." . $ext[1];
            } else{
                $newname = "0_" . $id . "_pfp." . $ext[1];
            }

            //S'ajunta la carpeta amb el nom per crear un path definitiu
            $target_file = $target_dir . $newname;

            //Si es mes gran que 1Mb o no es png palme
            if ($_FILES['avatar']['size'] > 1000000) {
                $this->errors = 'File bigger than 1MB';
            } elseif ($ext[1] != "png") {
                $this->errors = 'File type not supported';
            }

            //Si no hi ha cap error procedeix a retallar la imatge si es mes gran que 400x400
            if ($this->errors == '') {

                $maxSize = 400;

                //Agafa les mides de la imatge
                list($width, $height, $type, $attr) = getimagesize($tmp);

                $newW = $width;
                $newH = $height;

                //Es retalle nomes si es mes gran
                if($width > $maxSize){
                    $newW = 400;
                }

                if($height > $maxSize){
                    $newH = 400;
                }

                //Guarda la imatge original
                $src = imagecreatefromstring(file_get_contents( $tmp ));
                //Cree una mascara
                $dst = imagecreatetruecolor( $newW, $newH);

                //Aplique la mascara
                imagecopyresampled( $dst, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);

                imagedestroy( $src );
                //Passe a png
                imagepng($dst, $tmp);
                imagedestroy( $dst );

                //Mou la imatge de la carpeta temporal a la que nosaltres volem i si funciona s'afegeix el path a la base de dades
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file)) {
                    $this->container->get('user_repository')->updatePfp($id, $target_file);
                    $this->container->get('user_repository')->updateModifyDate($id);
                } else {
                    $this->errors = 'Could not upload the file;';
                }
            } else {
                return $this->showProfile($request, $response);
            }
        }

        return $this->showProfile($request, $response);
    }

    public function showProfile(Request $request, Response $response): Response
    {
        //El middleware no funciona si no hi ha aixo una altra vegada i es fa servir $_SESSION['user_id']
        if (empty($_SESSION['user_id'])) {
            return $response->withHeader('Location', '/sign-in')->withStatus(302);
        }

        $id = $_SESSION['user_id'];

        //Agafa totes les dades de la bbdd
        $path = $this->container->get('user_repository')->getInfoById('pfp_path', $id);
        if($path == 'Unknown') $path = "https://placehold.it/400x400";
        $email = $this->container->get('user_repository')->getInfoById('email', $id);
        $birth = $this->container->get('user_repository')->getInfoById('birthday', $id);

        $messages = $this->container->get('flash')->getMessages();

        $notifications = $messages['pass'] ?? [];

        return $this->container->get('view')->render(
            $response,
            'profile.twig',
            [
                'user_id'=>$_SESSION['user_id'],
                'email'=>$email,
                'birth'=>$birth,
                'pfp' => $path,
                'errors' => $this->errors,
                'nots' => $notifications
            ]
        );
    }
}