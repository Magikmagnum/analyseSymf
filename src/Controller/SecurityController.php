<?php

namespace App\Controller;

use App\Entity\User;
use OpenApi\Annotations as OA;
use App\Repository\UserRepository;
use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 *  
 * @OA\Post(
 *  path="/api/v1/login_check",
 *  tags={"Securities"},
 *  @OA\RequestBody(
 *      request="Login",
 *      description="Corp de la requete",
 *      required=true,
 *      @OA\JsonContent(
 *          @OA\Property(type="string", property="username", example="coucou@exemple.com"),
 *          @OA\Property(type="string", property="password", required=true, example="emileA15ans"),
 *      )
 *  ), 
 * 
 *  @OA\Response(
 *      response="200",
 *      description="Authentification",
 *      @OA\JsonContent(
 *          allOf={@OA\Schema(ref="#/components/schemas/Success")},
 *          @OA\Property(type="objet", property="data", ref="#/components/schemas/Login"),
 *      ),
 *  ),
 * 
 *  @OA\Response( response="400", ref="#/components/responses/BadRequest" ),
 *  @OA\Response( response="403", ref="#/components/responses/ForBidden" ),
 *  @OA\Response( response="404", ref="#/components/responses/NotFound" ),
 * 
 * )
 * 
 */
class SecurityController extends AbstractController
{


    /**
     * 
     * @Route("/register", name="security_register", methods={"POST"})
     * 
     * @OA\Post(
     *  path="/api/v1/register",
     *  tags={"Securities"},
     *  @OA\RequestBody(
     *      request="Register",
     *      description="Corp de la requete",
     *      required=true,
     *      @OA\JsonContent(
     *          @OA\Property(type="string", property="email", example="coucou@exemple.com"),
     *          @OA\Property(type="string", property="password", required=true, example="emileA15ans"),
     *      )
     *  ), 
     * 
     *  @OA\Response(
     *      response="201",
     *      description="Inscription",
     *      @OA\JsonContent(ref="#/components/schemas/Security"),
     *  ),
     * 
     *  @OA\Response( response="400", ref="#/components/responses/BadRequest" ),
     *  @OA\Response( response="403", ref="#/components/responses/ForBidden" ),
     *  @OA\Response( response="404", ref="#/components/responses/NotFound" ),
     * 
     * )
     */
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent());
        $errors = [];

        $user = new User();
        $user->setEmail($data->email);
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($data->password);

        if (!$errors = $this->getErrorUser($user)) {
            $user->setPassword($passwordHasher->hashPassword($user, $data->password));

            $this->getManager()->persist($user);
            $this->getManager()->flush();

            // creation du token et initialisation dans le attribut password
            $user->setToken($this->jWTManager->create($user));
            $response = $this->statusCode(Response::HTTP_CREATED, $user);

            return $this->json($response, $response["status"], [], ["groups" => "read:auth:item"]);
        }

        $response = $this->statusCode(Response::HTTP_BAD_REQUEST, $errors);
        return $this->json($response, $response["status"]);
    }








    /**
     * 
     * @Route("/reset", name="security_reset", methods={"PUT"})
     * 
     * @OA\Put(
     *  path="/api/v1/reset",
     *  tags={"Securities"},
     *  @OA\RequestBody(
     *      request="Register",
     *      description="Corp de la requete",
     *      required=true,
     *      @OA\JsonContent(
     *          @OA\Property(type="string", property="email", example="coucou@exemple.com"),
     *          @OA\Property(type="string", property="newemail", example="coucou@exemple.com"),
     *          @OA\Property(type="string", property="password", required=true, example="emileA15ans"),
     *          @OA\Property(type="string", property="newpassword", required=true, example="emileA15ans"),
     *      )
     *  ), 
     * 
     *  @OA\Response(
     *      response="200",
     *      description="Modification du mot de passe",
     *      @OA\JsonContent(ref="#/components/schemas/Security"),
     *  ),
     * 
     *  @OA\Response( response="400", ref="#/components/responses/BadRequest" ),
     *  @OA\Response( response="403", ref="#/components/responses/ForBidden" ),
     *  @OA\Response( response="404", ref="#/components/responses/NotFound" ),
     * 
     * )
     */
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $data = json_decode($request->getContent());
        $errors = $this->getErrorAuth($request, $passwordHasher);
        if (empty($errors)) {

            $user = $this->getUser();

            // on verifie si le nouveau couple email, passe word est valide
            $userForCheck = new User();
            isset($data->newemail) ? $userForCheck->setEmail($data->newemail) : $userForCheck->setEmail($user->getEmail());
            isset($data->newpassword) ? $userForCheck->setPassword($data->newpassword) : $userForCheck->setPassword($user->getPassword());
            $userForCheck->setRoles(['ROLE_USER']);


            if (!$errors = $this->getErrorUser($userForCheck)) {

                // on persiste les données dans la base de données.
                isset($data->newemail) && $user->setEmail($data->newemail);
                isset($data->newpassword) && $user->setPassword($passwordHasher->hashPassword($user, $data->newpassword));
                $this->getManager()->persist($user);
                $this->getManager()->flush();

                // creation du token et initialisation dans le attribut password
                $user->setToken($this->jWTManager->create($user));
                $response = $this->statusCode(Response::HTTP_OK, $user);

                return $this->json($response, $response["status"], [], ["groups" => "read:auth:item"]);
            }
        }

        $response = $this->statusCode(Response::HTTP_BAD_REQUEST, $errors);
        return $this->json($response, $response["status"]);
    }





    /**
     * 
     * @Route("/delete", name="security_delete", methods={"DELETE"})
     * 
     * @OA\Delete(
     *  path="/api/v1/delete",
     *  tags={"Securities"},
     *  @OA\RequestBody(
     *      request="Register",
     *      description="Corp de la requete",
     *      required=true,
     *      @OA\JsonContent(
     *          @OA\Property(type="string", property="email", example="coucou@exemple.com"),
     *          @OA\Property(type="string", property="password", required=true, example="emileA15ans"),
     *      )
     *  ), 
     * 
     *  @OA\Response(
     *      response="200",
     *      description="Suppression du mot de passe",
     *      @OA\JsonContent(ref="#/components/schemas/Security"),
     *  ),
     * 
     *  @OA\Response( response="400", ref="#/components/responses/BadRequest" ),
     *  @OA\Response( response="403", ref="#/components/responses/ForBidden" ),
     *  @OA\Response( response="404", ref="#/components/responses/NotFound" ),
     * 
     * )
     */
    public function delete(Request $request, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $errors = $this->getErrorAuth($request, $passwordHasher, false);
        if (empty($errors)) {

            $user = $this->getUser();
            $userRepository->remove($user, true);

            $response = $this->statusCode(Response::HTTP_OK, []);
            return $this->json($response, $response["status"], [], ["groups" => "read:auth:item"]);
        }

        $response = $this->statusCode(Response::HTTP_BAD_REQUEST, $errors);
        return $this->json($response, $response["status"]);
    }





    /**
     * la methode qui valide l'object user.
     *
     * @param UserInterface $user
     * @return array|false
     */
    private function getErrorUser($user): array | null
    {
        $errors = [];

        //verification des erreurs email
        $email = $user->getEmail();
        !isset($email) && $errors[] = ['path' => "email", 'message' => "Champs obligatoir"];

        //verification des erreur sur le nouveau mot de passe
        $errorPassword = "Champs obligatoir";
        $password = $user->getPassword();
        if (!isset($password) || $errorPassword = $this->check->validatePassword($user->getPassword())) {
            $errors[] = ['path' => "password", 'message' => $errorPassword];
        }

        if (empty($errors)) {
            if (!$errors = $this->getOrmValidationErrors($user, $errors)) {
                return null;
            }
        }
        return $errors;
    }


    /**
     * Undocumented function
     *
     * @param Request $request
     * @param UserPasswordHasherInterface $passwordHasher
     * @param boolean $flag
     * @return array|null
     */
    private function getErrorAuth(Request $request, UserPasswordHasherInterface $passwordHasher, $flag = true): array|null
    {
        $data = json_decode($request->getContent());
        $errors = [];

        $flag ? $path = 'old' : $path = '';

        //recupere l'utilisateur courant
        $user = $this->getUser();
        // on verifie si email de l'utilisateur est le meme que l'email passer en parametre
        $oldEmail = $user->getEmail();

        if (isset($data->email)) {
            !($oldEmail == $data->email) && $errors[] = ['path' => $path . "email", 'message' => "email incorrect"];
        } else {
            $errors[] = ['path' => $path . "email", 'message' => "Champs obligatoir"];
        }

        //on verifie si le mot de passe de l'utilisateur est le meme que le mot de passe passer en parametre
        if (isset($data->password)) {
            !$passwordHasher->isPasswordValid($user, $data->password) &&  $errors[] = ['path' => $path . "password", 'message' => "mot de passe incorrect"];
        } else {
            $errors[] = ['path' => $path . "password", 'message' => "Champs obligatoir"];
        }

        if (empty($errors)) {
            return [];
        }

        return $errors;
    }
}

