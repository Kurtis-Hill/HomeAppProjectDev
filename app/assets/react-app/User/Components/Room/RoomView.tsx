import * as React from 'react';
import { useState, useMemo } from 'react';
import { useMainIndicators } from '../../../Common/Components/Pages/MainPageTop';
import {NavigateFunction, useNavigate, useParams} from "react-router-dom";
import RoomResponseInterface from "../../Response/Room/RoomResponseInterface";
import {getRoomRequest} from "../../Request/Room/GetRoomRequest";
import DotCircleSpinner from "../../../Common/Components/Spinners/DotCircleSpinner";
import {TabSelector} from "../../../Common/Components/TabSelector";
import {CardRowContainer} from "../../../UserInterface/Components/CardRowContainer";
import {UpdateRoom} from "./UpdateRoom";
import {AxiosError} from "axios";
import {indexUrl} from "../../../Common/URLs/CommonURLs";

export function RoomView() {
    const tabOptions = ['Card View', 'Edit', 'Commands'];

    const { setRefreshNavbar } = useMainIndicators();

    const params = useParams();
    const roomID: number = parseInt(params.roomID);

    const [roomData, setRoomData] = useState<RoomResponseInterface|null>(null);

    const [roomLoading, setRoomLoading] = useState<boolean>(true);

    const [currentTab, setCurrentTab] = useState<string>(tabOptions[0]);

    const navigate: NavigateFunction = useNavigate();

    const getRoomData = async () => {
        try {
            const getRoomResponse = await getRoomRequest(roomID);
            const roomData: RoomResponseInterface = getRoomResponse.data.payload;
            setRoomData(roomData);
        } catch (error) {
            const err = error as AxiosError
            if (err.response?.status === 404) {
                navigate(`${indexUrl}`)
            }
        }
        setRoomLoading(false);
    }

    useMemo(() => {
        getRoomData();
    }, [roomID]);

    if (roomLoading === true || roomData === null) {
        return <DotCircleSpinner spinnerSize={5} classes="center-spinner" />
    }

    return (
        <>
            <div className="container" style={{ textAlign: "center", margin: "inherit"}}>
                <TabSelector
                    options={tabOptions}
                    currentTab={currentTab}
                    setCurrentTab={setCurrentTab}
                />
                {
                    currentTab === tabOptions[0]
                        ?
                            <CardRowContainer route={`room/${roomID}`} />
                        :
                            null
                }
                {
                    currentTab === tabOptions[1]
                        ?
                            <UpdateRoom
                                roomData={roomData}
                                setRefreshNavbar={setRefreshNavbar}
                                setRoomData={setRoomData}
                            />
                        :
                            null
                }
                {
                    currentTab === tabOptions[2]
                        ?
                            <div>
                                <h1>Commands</h1>
                            </div>
                        :
                            null
                }
            </div>
        </>
     )
}
