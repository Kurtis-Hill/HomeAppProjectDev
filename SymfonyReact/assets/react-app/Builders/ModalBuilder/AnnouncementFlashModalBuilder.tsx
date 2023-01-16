import * as React from 'react';

import { AnnouncementFlashModal } from '../../Components/Modals/AnnouncementFlashModal';

export function BuildAnnouncementErrorFlashModal(props: {
      announcementModals: Array<typeof AnnouncementFlashModal>;
      setAnnouncementModals: (announcementModals: Array<typeof AnnouncementFlashModal>) => void;
      title: string;
      dataToList: string[];
      dataNumber: number;
      timer?: number|null;
  }) {
  const setAnnouncementModals = props.setAnnouncementModals;
  const title: string = props.title
  const dataToList: Array<string> = props.dataToList
  const dataNumber: number = props.dataNumber
  const timer: number = props.timer ?? 80;

  return (
      <React.Fragment>
        <AnnouncementFlashModal
            setAnnouncementModals={setAnnouncementModals}
            title={title}
            errors={dataToList}
            errorNumber={dataNumber}
            timer={timer}
        />
      </React.Fragment>
  );
}
