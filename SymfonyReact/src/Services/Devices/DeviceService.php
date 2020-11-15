<?php


namespace App\Services\Devices;


use App\Entity\Core\Devices;
use App\Form\SensorForms\AddNewDeviceForm;
use App\HomeAppCore\HomeAppRoomAbstract;
use Doctrine\ORM\ORMException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class DeviceService extends HomeAppRoomAbstract
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
        $currentUserDeviceCheck = $this->em->getRepository(Devices::class)->findOneBy(['deviceName' => $deviceData['deviceName']]);

        if (!empty($currentUserDeviceCheck)) {
            $this->errors[] = 'Your group already has a device named'. $deviceData['device-name'];
        }

        if (!in_array($deviceData['device-group'], $this->getGroupNameID())) {
            $this->errors[] = 'You are not part of this group';
        }

        return $this->processNewDeviceForm($addNewDeviceForm, $deviceData);
    }

    /**
     * @param FormInterface $addNewDeviceForm
     * @param array $deviceName
     * @return array|FormInterface
     */
    private function processNewDeviceForm(FormInterface $addNewDeviceForm, array $deviceName)
    {
        $addNewDeviceForm->submit([$deviceName]);

        if ($addNewDeviceForm->isSubmitted() && $addNewDeviceForm->isValid()) {
            $deviceName .= time();
            $secret = hash("md5", $deviceName);

            $validFormData = $addNewDeviceForm->getData();
            $validFormData->setSecret($secret);

            try {
                $this->em->persist($validFormData);
                $this->em->flush();
            } catch (ORMException $e) {
                $errors['errors'] = $e->getMessage();
            } catch(\PDOException $e){
                $errorMessage['errors'] = $e->getMessage();
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();

                return $errors;
            }
        }

        return $addNewDeviceForm;
    }

    public function returnAllErrors()
    {
        return $this->errors;
    }
}