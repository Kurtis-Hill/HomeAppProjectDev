import * as React from 'react';
import { useState, useEffect } from 'react';
import { SensorTriggerResponseInterface } from '../../Sensors/Response/Sensor/Trigger/SensorTriggerResponseInterface';
import { getAllSensorTriggerTypesRequest } from '../../Sensors/Request/Trigger/GetAllTriggersRequest';
import BaseModal from '../../Common/Components/Modals/BaseModal';
import SubmitButton from '../../Common/Components/Buttons/SubmitButton';
import CloseButton from '../../Common/Components/Buttons/CloseButton';
import TriggerCard from '../../Sensors/Components/Trigger/TriggerCard';
import DotCircleSpinner from '../../Common/Components/Spinners/DotCircleSpinner';
import { deleteTriggerRequest } from '../../Sensors/Request/Trigger/DeleteTriggerRequest';
import { BaseCard } from '../../UserInterface/Cards/Components/BaseCard';
import TriggerForm from '../../Sensors/Components/Trigger/TriggerForm';
import UpdateTrigger from "../../Sensors/Components/Trigger/UpdateTrigger";
import { addNewTriggerForm } from '../../Sensors/Request/Trigger/AddNewTriggerRequest';

export default function TriggerPage() {
    const [triggerData, setTriggerData] = useState<SensorTriggerResponseInterface[]>([]);

    const [loadingTriggerData, setLoadingTriggerData] = useState<boolean>(true);

    const [triggerDataErrors, setTriggerDataErrors] = useState<Array<string>>([]);

    const [addNewModal, setAddNewModal] = useState<boolean>(false);

    const [showDeleteModal, setShowDeleteModal] = useState<boolean>(false);

    const [showUpdateModal, setShowUpdateModal] = useState<boolean>(false);

    const [updateTriggerID, setUpdateTriggerID] = useState<number|null>(null);

    const [selectedTriggerID, setSelectedTriggerID] = useState<number|null>(null);

    const fetchAllTriggerData = async () => {
        setLoadingTriggerData(true);
        const response = await getAllSensorTriggerTypesRequest();
        console.log(response.data.payload);
        if (response.status === 200 && Array.isArray(response.data.payload)) {
            setTriggerData(response.data.payload);
            setLoadingTriggerData(false);
        } else {
            setLoadingTriggerData(false);
        }
    }

    const handleShowDeleteModal = (triggerID: number|null) => {
        if (triggerID === null) {
            setShowDeleteModal(false);    
        } else {
            setSelectedTriggerID(triggerID);
            setShowDeleteModal(true);
        }
    }


    const deleteTrigger = async (e: Event) => {
        const response = await deleteTriggerRequest(selectedTriggerID);
        if (response.status === 200) {
            await fetchAllTriggerData();
        }
    } 

    const handleSendNewTriggerRequest = async (e: Event) => {
        e.preventDefault();
        console.log('send new trigger request');

        if (newTriggerRequest.sensorThatTriggers === 0 && newTriggerRequest.sensorToBeTriggered === 0) {
            alert('You must select a sensor that triggers or a sensor to be triggered');
            return;
        }

        //run through the days and remove any that are false
        let days = [];
        if (newTriggerRequest.days.monday === true) {
            days.push(DaysEnum.Monday);
        }
        if (newTriggerRequest.days.tuesday === true) {
            days.push(DaysEnum.Tuesday);
        }
        if (newTriggerRequest.days.wednesday === true) {
            days.push(DaysEnum.Wednesday);
        }
        if (newTriggerRequest.days.thursday === true) {
            days.push(DaysEnum.Thursday);
        }
        if (newTriggerRequest.days.friday === true) {
            days.push(DaysEnum.Friday);
        }
        if (newTriggerRequest.days.saturday === true) {
            days.push(DaysEnum.Saturday);
        }
        if (newTriggerRequest.days.sunday === true) {
            days.push(DaysEnum.Sunday);
        }

        console.log('days', days)

        const addNewTriggerRequest: AddNewTriggerType = {
            operator: newTriggerRequest.operator,
            triggerType: newTriggerRequest.triggerType,
            baseReadingTypeThatTriggers: newTriggerRequest.sensorThatTriggers !== 0 ? newTriggerRequest.sensorThatTriggers : null,
            baseReadingTypeThatIsTriggered: newTriggerRequest.sensorToBeTriggered !== 0 ? newTriggerRequest.sensorToBeTriggered : null,
            days: days,
            valueThatTriggers: newTriggerRequest.valueThatTriggers,
            startTime: newTriggerRequest.startTime,
            endTime: newTriggerRequest.endTime,
        }
        console.log('add new trigger request', addNewTriggerRequest)

        const response = await addNewTriggerForm(addNewTriggerRequest);

        if (response.status === 200) {
            closeForm(true);
            resetData();
        }
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
                                loadingTriggerData === true
                                    ?
                                        <DotCircleSpinner spinnerSize={5} classes="center-spinner-card-row hidden-scroll" />
                                    :                                        
                                        triggerData.length > 0
                                            ? 
                                                triggerData.map((sensorTriggerData: SensorTriggerResponseInterface, index: number) => {
                                                    return (
                                                        <React.Fragment key={index}>
                                                            <div>
                                                                <TriggerCard 
                                                                    sensorTriggerData={sensorTriggerData} 
                                                                    handleShowDeleteModal={handleShowDeleteModal}
                                                                    setTriggerToUpdate={setUpdateTriggerID}
                                                                    setShowUpdateModal={setShowUpdateModal}
                                                                    showUpdateModal={showUpdateModal}
                                                                    id={sensorTriggerData.sensorTriggerID}
                                                                />
                                                            </div>
                                                        </React.Fragment>
                                                    )
                                                })
                                            : 
                                                <h2>No Triggers to display</h2>

                            }
                            {
                                <BaseCard loading={false} setCardLoading={setAddNewModal} setVariableToUpdate={() => false}>
                                    <h2>+ Add New Trigger</h2>
                                </BaseCard>
                            }
                            {
                                addNewModal === true
                                ?
                                    <>
                                        <BaseModal
                                            title={'Add New Trigger'}
                                            modalShow={addNewModal}
                                            setShowModal={setAddNewModal}
                                            heightClasses="standard-modal-height"
                                        >
                                            <TriggerForm
                                                closeForm={setAddNewModal}
                                                resetData={fetchAllTriggerData}
                                                presets={null}
                                                handleTriggerRequest={handleSendNewTriggerRequest}
                                            />
                                        </BaseModal>
                                    </>
                                :
                                    null
                            }
                            {
                                showDeleteModal === true
                                
                                    ?
                                        <BaseModal
                                            title={`Delete Trigger`}
                                            modalShow={showDeleteModal}
                                            setShowModal={setShowDeleteModal}                                            
                                        >
                                            <>
                                                Delete trigger ID: <b>{selectedTriggerID}</b>?
                                                <br />
                                                <SubmitButton
                                                    type="submit"
                                                    text='Delete Device'
                                                    name='delete-device'
                                                    action='submit'
                                                    classes='add-new-submit-button'
                                                    onClickFunction={() => deleteTrigger}
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
                            {
                                showUpdateModal === true
                                    ?
                                        <>
                                            <BaseModal
                                                title={`Update Trigger`}
                                                modalShow={showUpdateModal}
                                                setShowModal={setShowUpdateModal}
                                                heightClasses="standard-modal-height"
                                            >
                                                <UpdateTrigger
                                                    setShowUpdateModal={setShowUpdateModal}
                                                    triggerID={updateTriggerID}
                                                    resetData={fetchAllTriggerData}
                                                />
                                            </BaseModal>
                                        </>
                                    :
                                        null
                            }
                            {
                                triggerDataErrors.length > 0
                                    ?   
                                        triggerDataErrors.map((error: string, index: number) => {
                                            return (
                                                <div key={index}>
                                                    <h2>{error}</h2>
                                                </div>
                                            )
                                        })
                                    :
                                        null
                            }
                    </div>
                </div>
            </div>
        </>
    );
}
