<?php


namespace App\Services\Devices;


use App\Entity\Core\Devices;
use App\Form\SensorForms\AddNewDeviceForm;
use App\HomeAppCore\HomeAppCoreAbstract;
use Doctrine\ORM\ORMException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class DeviceService extends HomeAppCoreAbstract
{
    /**
     * @var array
     */
    private $errors = [];

    /**
     * @param array $deviceData
     * @param FormInterface $addNewDeviceForm
     * @return array|FormInterface
     */
    public function handleNewDeviceSubmission(array $deviceData, FormInterface $addNewDeviceForm): FormInterface
    {
        $this->userInputDataCheck($deviceData);

        return $this->processNewDeviceForm($addNewDeviceForm, $deviceData);
    }

    private function userInputDataCheck(array $deviceData): void
    {
        $currentUserDeviceCheck = $this->em->getRepository(Devices::class)->findOneBy(['devicename' => $deviceData['devicename']]);

        if (!empty($currentUserDeviceCheck)) {
            $this->errors[] = 'Your group already has a device named'. $deviceData['devicename'];
        }

        if (!in_array($deviceData['groupnameid'], $this->getGroupNameID())) {
            $this->errors[] = 'You are not part of this group';
        }
    }

    /**
     * @param FormInterface $addNewDeviceForm
     * @param array $deviceData
     * @return bool|FormInterface
     */
    private function processNewDeviceForm(FormInterface $addNewDeviceForm, array $deviceData): FormInterface
    {
        $addNewDeviceForm->submit($deviceData);

        if ($addNewDeviceForm->isSubmitted() && $addNewDeviceForm->isValid()) {
            $secret = $deviceData['devicename'];
            $secret .= time();
            $secret = hash("md5", $secret);

            $validFormData = $addNewDeviceForm->getData();
            $validFormData->setSecret($secret);

            try {
                $this->em->persist($validFormData);
                $this->em->flush();
            } catch (ORMException $e) {
                error_log($e->getMessage());
            } catch(\PDOException $e){
                $errorMessage['errors'] = $e->getMessage();
            } catch (\Exception $e) {
                error_log($e->getMessage());
            }
        }
        else {
            foreach ($addNewDeviceForm->getErrors(true, true) as $error) {
                array_push($this->errors, $error->getMessage());
            }
        }

        return $addNewDeviceForm;
    }



    public function returnAllErrors()
    {
        return $this->errors;
    }
}
