import * as React from 'react';
import { useState, useEffect, useRef } from 'react';
import RoomResponseInterface from "../../Response/Room/RoomResponseInterface";
import {AnnouncementFlashModal} from "../../../Common/Components/Modals/AnnouncementFlashModal";
import {AnnouncementFlashModalBuilder} from "../../../Common/Builders/ModalBuilder/AnnouncementFlashModalBuilder";
import {FormInlineInputWLabel} from "../../../Common/Components/Inputs/FormInlineInputWLabel";
import {FormInlineSpan} from "../../../Common/Components/Elements/FormInlineSpan";
import {checkAdmin} from "../../../Authentication/Session/UserSessionHelper";
import {AxiosError} from "axios";
import updateRoomRequest from "../../Request/Room/UpdateRoomRequest";

export type UpdateRoomFormInputs = {
    roomID?: number;
    roomName: string;
}

export function UpdateRoom(props: {
    setRoomData: (data: RoomResponseInterface) => void;
    setRefreshNavbar: (refresh: boolean) => void;
    roomData: RoomResponseInterface;
}) {
    const {setRoomData, setRefreshNavbar, roomData} = props;

    const [activeFormForUpdating, setActiveFormForUpdating] = useState({
        roomName: false,
    });

    const [roomUpdateFormInputs, setRoomUpdateFormInputs] = useState<UpdateRoomFormInputs>({
        roomName: roomData.roomName,
    });

    const originalRoomData = useRef<UpdateRoomFormInputs>({
        roomID: roomData.roomID,
        roomName: roomData.roomName,
    })

    const [announcementModals, setAnnouncementModals] = useState<React.JSX.Element[]>([]);

    const showAnnouncementFlash = (message: Array<string>, title: string, timer?: number | null): void => {
        setAnnouncementModals([
            <AnnouncementFlashModalBuilder
                setAnnouncementModals={setAnnouncementModals}
                title={title}
                dataToList={message}
                timer={timer ? timer : 40}
            />
        ])
    }

    useEffect(() => {
        if (roomData.roomID === originalRoomData.current.roomID) {
            setRoomUpdateFormInputs({
                roomName: roomData.roomName,
            });
            originalRoomData.current = {
                roomID: roomData.roomID,
                roomName: roomData.roomName,
            }
        }
    }, [roomData.roomID, announcementModals, activeFormForUpdating])

    const toggleFormInput = (event: Event) => {
        const name = (event.target as HTMLElement | HTMLInputElement).dataset.name !== undefined
            ? (event.target as HTMLElement | HTMLInputElement).dataset.name
            : (event.target as HTMLInputElement).name;

        setActiveFormForUpdating({
            ...activeFormForUpdating,
            [name]: !activeFormForUpdating[name],
        });

        setRoomUpdateFormInputs({
            ...roomUpdateFormInputs,
            [name]: originalRoomData.current[name],
        });
    }

    const handleUpdateRoomInput = (event: Event) => {
        const name = (event.target as HTMLInputElement).name;
        const value = (event.target as HTMLInputElement).value;

        setRoomUpdateFormInputs({
            ...roomUpdateFormInputs,
            [name]: value,
        });
    }

    const sendUpdateRoomRequest = async (e: Event) => {
        const name = (e.target as HTMLElement).dataset.name;
        let dataToSend: { roomName?: string } = {};

        switch (name) {
            case 'roomName':
                dataToSend = {
                    roomName: roomUpdateFormInputs.roomName,
                }
                break;
            default:
                break;
        }

        try {
            const roomUpdateResponse = await updateRoomRequest(roomData.roomID, dataToSend);

            if (roomUpdateResponse.status === 200) {
                const roomUpdateResponseData = roomUpdateResponse.data.payload;
                setRefreshNavbar(true);
                showAnnouncementFlash(['Room Updated Successfully'], 'Success');

                setRoomUpdateFormInputs({
                    ...roomUpdateFormInputs,
                    roomName: roomUpdateResponseData.roomName,
                });

                setActiveFormForUpdating({
                    ...activeFormForUpdating,
                    roomName: false,
                });
                setRoomData(roomUpdateResponseData);

            }
        } catch (error) {
            const err = error as AxiosError;
            if (err.response?.status === 400) {
                showAnnouncementFlash([err.message], 'Error', 20);
            } else {
                showAnnouncementFlash(['An error occurred. Please try again later.'], 'Error', 20);
            }
        }
    }

    return (
        <>
            <form>
                <div className="row"
                     style={{paddingTop: "4%"}}
                >
                    {
                        activeFormForUpdating.roomName === true && checkAdmin() === true
                            ?
                                <FormInlineInputWLabel
                                    labelName='Room Name: '
                                    nameParam='roomName'
                                    changeEvent={handleUpdateRoomInput}
                                    value={roomUpdateFormInputs.roomName}
                                    acceptClickEvent={(e: Event) => sendUpdateRoomRequest(e)}
                                    declineClickEvent={(e: Event) => toggleFormInput(e)}
                                    dataName='roomName'
                                />
                            :
                                <FormInlineSpan
                                    spanOuterTag={'Room Name:'}
                                    spanInnerTag={roomData.roomName}
                                    clickEvent={(e: Event) => toggleFormInput(e)}
                                    dataName={'roomName'}
                                    canEdit={checkAdmin()}
                                />
                    }
                </div>
            </form>
        </>
    )
}
