<?php


namespace App\Services\ESPDeviceSensor\Devices;


use App\Entity\Devices\Devices;

use App\HomeAppSensorCore\ESPDeviceSensor\AbstractHomeAppUserSensorServiceCore;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;


class DeviceServiceUser extends AbstractHomeAppUserSensorServiceCore
{
    /**
     * @param array $deviceData
     * @param FormInterface $addNewDeviceForm
     * @return FormInterface
     */
    public function handleNewDeviceSubmission(array $deviceData, FormInterface $addNewDeviceForm): FormInterface
    {
     //   try {
            $this->userInputDataCheck($deviceData);
            $this->processNewDeviceForm($addNewDeviceForm, $deviceData);
//        }
//        catch (BadRequestException $e) {
//            $this->userInputErrors[] = $e->getMessage();
//        }
//        catch (ORMException $e) {
//            $this->serverErrors[] = $e->getMessage();
//            error_log($e->getMessage());
//        }
//        catch (\Exception $e) {
//            $this->serverErrors[] = 'Something went wrong';
//            error_log($e->getMessage());
//        }

        return $addNewDeviceForm;
    }

    private function userInputDataCheck(array $deviceData): void
    {
        $currentUserDeviceCheck = $this->em->getRepository(Devices::class)->findDeviceInUsersGroup($deviceData);

        if ($currentUserDeviceCheck instanceof Devices) {
            throw new BadRequestException(
                sprintf('Your group already has a device named %s that is in room %s',
                    $currentUserDeviceCheck->getDeviceName(),
                    $currentUserDeviceCheck->getRoomObject()->getRoom()
                )
            );
        }

        if (!in_array($deviceData['groupNameObject'], $this->getGroupNameIDs(), true)) {
            throw new BadRequestException(
                'You are not part of this group'
            );
        }
    }

    /**
     * @param FormInterface $addNewDeviceForm
     * @param array $deviceData
     * @return FormInterface
     */
    private function processNewDeviceForm(FormInterface $addNewDeviceForm, array $deviceData): FormInterface
    {
        $addNewDeviceForm->submit($deviceData);

        if ($addNewDeviceForm->isSubmitted() && $addNewDeviceForm->isValid()) {
            $secret = $deviceData['deviceName'];
            $secret .= time();
            $secret .= $deviceData['groupNameObject'];
            $secret .= $deviceData['roomObject'];

//            $secret = hash("md5", $secret);

            $validFormData = $addNewDeviceForm->getData();
            $validFormData->setDeviceSecret($secret);
            $validFormData->setCreatedBy($this->getUser());
            $validFormData->setRoles(['ROLE_DEVICE']);

//            $this->em->persist($validFormData);
//            $this->em->flush();
        }
        else {
            foreach ($addNewDeviceForm->getErrors(true, true) as $error) {
                $this->userInputErrors[] = $error->getMessage();
            }
        }

        return $addNewDeviceForm;
    }
}
