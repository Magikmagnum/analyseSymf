<?php

namespace App\Controller;

use OpenApi\Annotations as OA;
use App\Controller\Helpers\CheckHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

/**
 * 
 * 
 * @OA\Parameter(
 *      name="id",
 *      in="path",
 *      description="ID de la resource",
 *      required=true,
 *      @OA\Schema(type="integer"),
 * )
 * 
 * @OA\Schema(
 *      schema="Created",
 *      description="Created",
 *      @OA\Property(property="status", type="integer", example=201),
 *      @OA\Property(type="boolean", property="success", example=true),
 *      @OA\Property(property="message", type="string", example="Ressource créer avec succès"),
 *     
 * )
 * 
 * 
 * @OA\Schema(
 *      schema="Success",
 *      description="Success",
 *      @OA\Property(property="status", type="integer", example=200),
 *      @OA\Property(type="boolean", property="success", example=true),
 *      @OA\Property(property="message", type="string", example="Requète effectué avec succès"),
 *     
 * )
 * 
 * 
 * @OA\Response(
 *  response="NotFound",
 *  @OA\JsonContent(
 *      @OA\Property(property="status", type="integer", example=404),
 *      @OA\Property(type="boolean", property="success", example=false),
 *      @OA\Property(property="message", type="string", example="Ressource inexistante"),
 *  )
 * ),
 * 
 * 
 * 
 * @OA\Response(
 *  response="Unauthorized",
 *  @OA\JsonContent(
 *      @OA\Property(property="status", type="integer", example=401),
 *      @OA\Property(type="boolean", property="success", example=false),
 *      @OA\Property(property="message", type="string", example="Impossible de vous authentifier"),
 *  )
 * ),
 * 
 * 
 * 
 * @OA\Response(
 *  response="BadRequest",
 *  @OA\JsonContent(
 *      @OA\Property(property="status", type="integer", example=400),
 *      @OA\Property(type="boolean", property="success", example=false),
 *      @OA\Property(property="message", type="string", example="Requète invalide"),
 *  )
 * ),
 * 
 * 
 * 
 * @OA\Response(
 *  response="ForBidden",
 *  @OA\JsonContent(
 *      @OA\Property(property="status", type="integer", example=403),
 *      @OA\Property(type="boolean", property="success", example=false),
 *      @OA\Property(property="message", type="string", example="Vous n'avez pas les droits requis"),
 *  )
 * ),
 * 
 * 
 * 
 * @OA\SecurityScheme(bearerFormat="JWT", type="apiKey", securityScheme="bearer"),
 */
class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    protected $check;
    protected $listener;
    protected $prefix;
    protected $sufix;
    protected $validator;
    protected $jWTManager;
    protected $entityManager;

    public function __construct(ValidatorInterface $validator, ManagerRegistry $entityManager, CheckHelper $checkHelper, JWTTokenManagerInterface $jWTManager)
    {
        $this->check = $checkHelper;
        $this->jWTManager = $jWTManager;
        $this->prefix = "efdjflkxog5f6f@9gds2157b3";
        $this->sufix = "28d54grg!fv4d5g4eq5v5gvsdf";
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }


    public function getDatime($var = 'now')
    {
        return new \DateTime($var, new \DateTimeZone('Africa/Libreville'));
    }


    /**
     * La methode valide les données de l'objet par rapport au contrainte de la class entity.     
     *
     * @param [type] $entity
     * @param array|null $errors
     * @return array|false
     */
    public function getOrmValidationErrors($entity, ?array $errors = []): array | false
    {
        $validator = $this->validator->validate($entity);

        if (count($validator) > 0) {

            $ormValidationError = [];

            foreach ($validator as $val) {
                $ormValidationError[] = [
                    'path' => $val->getPropertyPath(),
                    'message' => $val->getMessage()
                ];
            }

            if ($errors) {
                return array_merge($errors, $ormValidationError);
            }

            return $ormValidationError;
        }

        if ($errors) {
            return $errors;
        }

        return false;
    }


    // La methode fournit l'objet entity manager de Doctrine
    public function getManager()
    {
        return $this->entityManager->getManager();
    }

    /**
     * return a response type array
     *  gestion des erreurs HTTP responses
     *
     * @param int $statusCode
     * @param array $data
     * @param string|null $message
     * @return array
     */
    public function statusCode($statusCode, $data = [], string $message = null)
    {
        switch ($statusCode) {

            case Response::HTTP_CREATED:

                $message === null && $message = "Ressource créee avec succès";
                return $this->response(true, $statusCode, $data, $message);
                break;

            case Response::HTTP_OK:

                $message === null && $message = "Operation reussie";
                return $this->response(true, $statusCode, $data, $message);
                break;

            case Response::HTTP_BAD_REQUEST:

                $message === null && $message = "Requète invalide";
                return $this->response(false, $statusCode, $data, $message);
                break;

            case Response::HTTP_UNAUTHORIZED:

                $message === null && $message = "Impossible de vous authentifier, veuillez vous connecter";
                return $this->response(false, $statusCode, $data, $message);
                break;

            case Response::HTTP_FORBIDDEN:

                $message === null && $message = "Vous n'avez pas les droits requis pour continuer cette action";
                return $this->response(false, $statusCode, $data, $message);
                break;

            case Response::HTTP_NOT_FOUND:

                $message === null && $message = "Route ou ressource inexistante, vérifier le lien de la requête";
                return $this->response(false, $statusCode, $data, $message);
                break;

            case Response::HTTP_NOT_MODIFIED:

                $message === null && $message = "Ressource non modifier";
                return $this->response(false, $statusCode, $data, $message);
                break;


            case Response::HTTP_UNSUPPORTED_MEDIA_TYPE:

                $message === null && $message = "Ressource pas supporté";
                return $this->response(false, $statusCode, $data, $message);
                break;
        }
    }





    ///-------------------  private methods --------------------------------






    private function response($success, $statusCode, $data = [], $message = null)
    {
        $response = ["status" => $statusCode, "success" => $success];
        $data ? $response["data"] = $data : null;
        $message ? $response["message"] = $message : null;
        return $response;
    }

    protected function idEncode($id)
    {
        return $this->prefix . $id . $this->sufix;
    }

    protected function idDecode($id)
    {
        $sansPrefix = substr($id, strlen($this->prefix));
        $sansSufix = substr($sansPrefix, 0, -strlen($this->sufix));
        return $sansSufix;
    }
}
