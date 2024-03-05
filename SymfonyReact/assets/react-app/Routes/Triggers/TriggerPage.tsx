import * as React from 'react';
import { useState, useEffect } from 'react';
import { SensorTriggerResponseInterface } from '../../Sensors/Response/Sensor/Trigger/SensorTriggerResponseInterface';
import { getAllSensorTriggerTypesRequest } from '../../Sensors/Request/Sensor/Trigger/GetAllTriggersRequest';
import { BaseCard } from '../../UserInterface/Cards/Components/BaseCard';
import DeleteButton from '../../Common/Components/Buttons/DeleteButton';
import BaseModal from '../../Common/Components/Modals/BaseModal';
import SubmitButton from '../../Common/Components/Buttons/SubmitButton';
import CloseButton from '../../Common/Components/Buttons/CloseButton';

export default function TriggerPage() {
    const [triggerData, setTriggerData] = useState<SensorTriggerResponseInterface[]>([]);

    const [loadingTriggerData, setLoadingTriggerData] = useState<boolean>(true);

    const [triggerDataErrors, setTriggerDataErrors] = useState<Array<string>>([]);

    const [addNewModal, setAddNewModal] = useState<boolean>(false);

    const [showDeleteModal, setShowDeleteModal] = useState<boolean>(false);

    const [selectedTriggerID, setSelectedTriggerID] = useState<number|null>(null);

    const fetchAllTriggerData = async () => {
        const response = await getAllSensorTriggerTypesRequest();
        if (response.status === 200) {
            setTriggerData(response.data.payload);
        } else {
            setTriggerDataErrors(response.data.errors);
        }
    }

    const deleteTrigger = async (Event: Event) => {
        // const response = await deleteTriggerRequest(triggerID);
        // if (response.status === 200) {
        //     fetchAllTriggerData();
        // }
    } 

    useEffect(() => {
        fetchAllTriggerData();
    }, []);

    return (
        <>  
            <div id="content-wrapper" className="d-flex flex-column">
                <div id="content"> 
                    <div className="container-fluid">
                        {/* <div className="row"> */}
                            <h1>Triggers</h1>
                            {
                                triggerData.map((sensorTriggerData: SensorTriggerResponseInterface, index: number) => {
                                    return (
                                        <>
                                            <div key={index}>
                                                <BaseCard loading={false}>
                                                    {
                                                        sensorTriggerData.baseReadingTypeThatTriggers
                                                            ? <span>Sensor that triggers: {sensorTriggerData.baseReadingTypeThatIsTriggered.sensor.sensorName}</span>
                                                            : null
                                                    }
                                                    <br />
                                                    {
                                                        sensorTriggerData.baseReadingTypeThatIsTriggered
                                                            ? <span>Sensor that is triggered: {sensorTriggerData.baseReadingTypeThatIsTriggered.sensor.sensorName}</span>
                                                            : null
                                                    }
                                                    <br />
                                                    <span>Value that triggers: {sensorTriggerData.valueThatTriggers}</span>
                                                    <br />
                                                    <span>Operator: {sensorTriggerData.operator.operatorSymbol}</span>
                                                    <br />
                                                    <span>Monday: {sensorTriggerData.monday === true ? 'true' : 'false'}</span>
                                                    <br />
                                                    <span>Tuesday: {sensorTriggerData.tuesday === true ? 'true' : 'false'}</span>
                                                    <br />
                                                    <span>Wednesday: {sensorTriggerData.wednesday === true ? 'true' : 'false'}</span>
                                                    <br />
                                                    <span>Thursday: {sensorTriggerData.thursday === true ? 'true' : 'false'}</span>
                                                    <br />
                                                    <span>Friday: {sensorTriggerData.friday === true ? 'true' : 'false'}</span>
                                                    <br />
                                                    <span>Saturday: {sensorTriggerData.saturday === true ? 'true' : 'false'}</span>
                                                    <br />
                                                    <span>Sunday: {sensorTriggerData.sunday === true ? 'true' : 'false'}</span>
                                                    <br />
                                                    <DeleteButton clickFunction={setShowDeleteModal}></DeleteButton>
                                                </BaseCard>
                                            </div>
                                            {
                                                showDeleteModal === true
                                                    ?
                                                        <BaseModal
                                                            title={`Delete Trigger`}
                                                            modalShow={true}
                                                            setShowModal={setShowDeleteModal}
                                                        >
                                                            <>
                                                            Delete trigger ID: {selectedTriggerID}?
                                                            <br />
                                                            <SubmitButton
                                                                type="submit"
                                                                text='Delete Device'
                                                                name='delete-device'
                                                                action='submit'
                                                                classes='add-new-submit-button'
                                                                onClickFunction={deleteTrigger}
                                                            />
                                                            <CloseButton 
                                                                close={setShowDeleteModal} 
                                                                classes={"modal-cancel-button"} 
                                                            />
                                                            </>                                                   
                                                        </BaseModal>
                                                    :
                                                        null
                                            }
                                        </>
                                    )
                                })
                            }
                    </div>
                </div>
            </div>
        </>
    );
}