import * as React from 'react';
import { AnnouncementFlashModal, AnnouncementFlashModalInterface } from "../../Components/Modals/AnnouncementFlashModal";
import { ResponseInterceptor } from '../Axios/ErrorResponseInterceptor';

export function ResponseComponent(props: { showAnnouncementFlash: (errors: string[], title: string, timer?: number) => void; announcementModals: ((props: AnnouncementFlashModalInterface) => any)[]; }) {
    const showAnnouncementFlash: (errors: Array<string>, title: string, timer?: number | null) => void | null = props.showAnnouncementFlash;

    return (
        <ResponseInterceptor showAnnouncementFlash={showAnnouncementFlash} />
    );
}
