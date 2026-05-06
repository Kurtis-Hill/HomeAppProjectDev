import * as React from 'react';
import { useState, useMemo } from 'react';
import { useMainIndicators } from '../../../Common/Components/Pages/MainPageTop';
import {NavigateFunction, useNavigate, useParams} from "react-router-dom";
import RoomResponseInterface from "../../Response/Room/RoomResponseInterface";
import DotCircleSpinner from "../../../Common/Components/Spinners/DotCircleSpinner";
import {TabSelector} from "../../../Common/Components/TabSelector";
import {CardRowContainer} from "../../../UserInterface/Components/CardRowContainer";
import {AxiosError} from "axios";
import {indexUrl} from "../../../Common/URLs/CommonURLs";
import GroupResponseInterface from "../../Response/Group/GroupResponseInterface";
import {checkAdmin} from "../../../Authentication/Session/UserSessionHelper";
import {getSingleUserGroupsRequest} from "../../Request/Group/GetSingleGroupRequest";

export function GroupView() {
    const tabOptions = ['Card View', 'Edit'];
    const admin = checkAdmin();

    if (!admin) {
        tabOptions.push('Admin');
    }

    const { setRefreshNavbar } = useMainIndicators();

    const params = useParams();
    const groupID: number = parseInt(params.groupID);

    const [groupData, setGroupData] = useState<GroupResponseInterface|null>(null);

    const [groupLoading, setGroupLoading] = useState<boolean>(true);

    const [currentTab, setCurrentTab] = useState<string>(tabOptions[0]);

    const navigate: NavigateFunction = useNavigate();

    const getGroupData = async () => {
        try {
            const getGroupResponse = await getSingleUserGroupsRequest(groupID);
            const groupData: GroupResponseInterface = getGroupResponse.data.payload;
            setGroupData(groupData);
        } catch (error) {
            const err = error as AxiosError
            if (err.response?.status === 404) {
                navigate(`${indexUrl}`)
            }
        }
        setGroupLoading(false);
    }

    useMemo(() => {
        getGroupData();
    }, [groupID]);

    if (groupLoading === true || groupData === null) {
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
                    currentTab === tabOptions[0] &&
                    <CardRowContainer
                        route={`group/${groupID}`}
                    />
                }
                {/*{*/}
                {/*    currentTab === tabOptions[1] &&*/}
                {/*    <UpdateGroup*/}
                {/*        groupID={groupID}*/}
                {/*        groupData={groupData}*/}
                {/*        setGroupData={setGroupData}*/}
                {/*    />*/}
                {/*}*/}
            </div>
        </>
    )
}
