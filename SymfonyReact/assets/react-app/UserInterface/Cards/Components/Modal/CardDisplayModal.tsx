import * as React from 'react';
import { useState, useEffect } from 'react';
import BaseModal from '../../../../Common/Components/Modals/BaseModal';
import { TabSelector } from '../../../../Common/Components/TabSelector';
import { UpdateCard } from '../Form/UpdateCard';
import { ReadingTypeDisplayTable } from '../../../../Sensors/Components/ReadingTypes/ReadingTypeDisplayTable';
import { getSensorsRequest, GetSensorsRequestType } from '../../../../Sensors/Request/Sensor/GetSensorsRequest';
import SensorResponseInterface from '../../../../Sensors/Response/Sensor/SensorResponseInterface';
import { RequestTypeEnum } from '../../../../Common/API/RequestTypeEnum';

const cardViewUpdate: string = 'Card View Update';
const boundaryUpdate: string = 'Boundary Update';

export function CardDisplayModal(props: {
    cardViewID: number|null;     
    loadingCardModalView: boolean;
    setLoadingCardModalView: (loadingCardModalView: boolean) => void;
}) {
    const { cardViewID, loadingCardModalView, setLoadingCardModalView } = props;
    const [tabSelection, setTabSelection] = useState<string>('Card View Update');
    const [sensorResponse, setSensorResponse] = useState<SensorResponseInterface|null>(null);

    const tabOptions = [
        cardViewUpdate,
        boundaryUpdate,
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
        if (tabSelection === 'Boundary Update') {
            setTabSelection(tabSelection);
            const sensorResponse = await handleGetSensorRequest(sensorRequestParameters);
            if (sensorResponse) {
                console.log('sensorResponse', sensorResponse)
                setSensorResponse(sensorResponse[0]);
            }
        } else {
            setTabSelection(tabSelection);
        }
    }

    const handleGetSensorRequest = async (getSensorParameters: GetSensorsRequestType): Promise<SensorResponseInterface> => {
        const getSensorsResponse = await getSensorsRequest(getSensorParameters);
        if (getSensorsResponse.status === 200) {
            return getSensorsResponse.data.payload;
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
                </div>
            </BaseModal>
        </>
    )
}
