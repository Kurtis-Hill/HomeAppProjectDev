<?php


namespace App\Services\Devices;


use App\Entity\Sensors\Devices;
use App\HomeAppSensorCore\AbstractHomeAppSensorServiceCore;
use Doctrine\ORM\ORMException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;


class DeviceService extends AbstractHomeAppSensorServiceCore
{
    /**
     * @var array
     */
    private array $serverErrors = [];

    private array $userInputErrors = [];

    /**
     * @param array $deviceData
     * @param FormInterface $addNewDeviceForm
     * @return FormInterface
     */
    public function handleNewDeviceSubmission(array $deviceData, FormInterface $addNewDeviceForm): FormInterface
    {
        try {
            $this->userInputDataCheck($deviceData);

        }
        catch (BadRequestException $e) {
            $this->userInputErrors[] = $e->getMessage();
        }
        catch (ORMException $e) {
            $this->serverErrors[] = 'Failed to process device query';
            error_log($e->getMessage());
        }
        catch (\Exception $e) {
            $this->serverErrors[] = 'Something went wrong';
            error_log($e->getMessage());
        }

        return $this->processNewDeviceForm($addNewDeviceForm, $deviceData);
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
            $secret = hash("md5", $secret);

            $validFormData = $addNewDeviceForm->getData();
            $validFormData->setDeviceSecret($secret);
            $validFormData->setCreatedBy($this->getUser());

            $this->em->persist($validFormData);
            $this->em->flush();
        }
        else {
            foreach ($addNewDeviceForm->getErrors(true, true) as $error) {
                $this->userInputErrors[] = $error->getMessage();
            }
        }

        return $addNewDeviceForm;
    }

    public function getAllUserErrors(): array
    {
        return $this->userInputErrors;
    }

    public function getAllServerErrors(): array
    {
        return $this->serverErrors;
    }
}
