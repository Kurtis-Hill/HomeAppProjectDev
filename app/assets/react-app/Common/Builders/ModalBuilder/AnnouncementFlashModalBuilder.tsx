import * as React from 'react';

import { AnnouncementFlashModal } from '../../Components/Modals/AnnouncementFlashModal';

export function AnnouncementFlashModalBuilder(props: {
  setAnnouncementModals: (announcementModals: Array<typeof AnnouncementFlashModal>) => void;
  title: string;
  dataToList?: string[];
  dataNumber?: number;
  timer?: number|null;
  setErrorCount?: (errorCount: number) => void;
}) {
  const setAnnouncementModals = props.setAnnouncementModals;
  const title: string = props.title
  const dataToList: Array<string> = props.dataToList ?? [];
  const dataNumber: number = props.dataNumber ?? 0;
  const timer: number = props.timer ?? 80;
  const setErrorCount = props.setErrorCount;

  return (
      <React.Fragment>
        <AnnouncementFlashModal
            setAnnouncementModals={setAnnouncementModals}
            title={title}
            errors={dataToList}
            errorNumber={dataNumber}
            timer={timer}
            modalShow={true}
            setErrorCount={setErrorCount}
        />
      </React.Fragment>
  );
}
