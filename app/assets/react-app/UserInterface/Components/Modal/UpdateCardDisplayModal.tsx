import * as React from 'react';
import { useState } from 'react';
import SensorResponseInterface from "../../../Sensors/Response/Sensor/SensorResponseInterface";
import {RequestTypeEnum} from "../../../Common/Request/RequestTypeEnum";
import {getSensorsRequest, GetSensorsRequestType} from "../../../Sensors/Request/Sensor/GetSensorsRequest";
import BaseModal from "../../../Common/Components/Modals/BaseModal";
import {TabSelector} from "../../../Common/Components/TabSelector";
import {UpdateCard} from "../Form/UpdateCard";
import {ReadingTypeDisplayTable} from "../../../Sensors/Components/ReadingTypes/ReadingTypeDisplayTable";
import {CommandsDisplay} from "../../../Sensors/Components/Commands/CommandsDisplay";

const cardViewUpdate: string = 'Card View Update';
const boundaryUpdate: string = 'Boundary Update';
const commands: string = 'Commands';

export function UpdateCardDisplayModal(props: {
    cardViewID: number|null;     
    loadingCardModalView: boolean;
    setLoadingCardModalView: (loadingCardModalView: boolean) => void;
}) {
    const { cardViewID, loadingCardModalView, setLoadingCardModalView } = props;
    const [tabSelection, setTabSelection] = useState<string>(cardViewUpdate);
    const [sensorResponse, setSensorResponse] = useState<SensorResponseInterface|null>(null);

    const tabOptions = [
        cardViewUpdate,
        boundaryUpdate,
        commands,
    ];

    const sensorRequestParameters = {
        cardViewIDs: [cardViewID],
        deviceIDs: [],
        deviceNames: [],
        limit: 1,
        page: 1,
        responseType: RequestTypeEnum.FULL,
    }

    const tabSelectionWrapper = async (tabSelection: string) => {
        setTabSelection(tabSelection);
        if (tabSelection === boundaryUpdate || tabSelection === commands) {
            const sensorResponse = await handleGetSensorRequest(sensorRequestParameters);
            if (sensorResponse) {
                setSensorResponse(sensorResponse);
            }
        }
    }

    const handleGetSensorRequest = async (getSensorParameters: GetSensorsRequestType): Promise<SensorResponseInterface> => {
        const getSensorsResponse = await getSensorsRequest(getSensorParameters);
        if (getSensorsResponse.status === 200) {
            return getSensorsResponse.data.payload[0];
        }
    }

    return (
        <>
            <BaseModal
                title={'Update'}
                modalShow={loadingCardModalView}
                setShowModal={setLoadingCardModalView}
            >
                <div className="container" style={{ textAlign: "center", margin: "inherit"}}>
                    <TabSelector
                        currentTab={tabSelection}
                        setCurrentTab={tabSelectionWrapper}
                        options={tabOptions}
                    />

                    {
                        tabSelection === tabOptions[0]
                            ? <UpdateCard
                                cardViewID={cardViewID}
                              />
                            : null
                    }            

                    {
                        tabSelection === tabOptions[1] && sensorResponse !== null
                            ?
                                <ReadingTypeDisplayTable
                                    canEdit={sensorResponse.canEdit}
                                    sensorReadingTypes={sensorResponse.sensorReadingTypes}
                                    refreshData={() => tabSelectionWrapper(tabSelection)}
                                />
                            : 
                                null

                    }    

                    {
                        tabSelection === tabOptions[2] && sensorResponse !== null
                            ?
                                <CommandsDisplay
                                    sensor={sensorResponse}
                                />
                            :
                                null
                    }
                </div>
            </BaseModal>
        </>
    )
}
