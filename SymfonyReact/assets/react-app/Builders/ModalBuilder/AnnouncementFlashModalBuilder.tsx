import * as React from 'react';

import { AnnouncementFlashModal } from '../../Components/Modals/AnnouncementFlashModal';

export function BuildAnnouncementErrorFlashModal(
    props: {
      title: string;
      dataToList: string[];
      dataNumber: number;
      timer?: number|null;
    }
  ) {
  const title: string = props.title
  const dataToList: Array<string> = props.dataToList
  const dataNumber: number = props.dataNumber
  const timer: number = props.timer ?? 80;

  return (
      <React.Fragment>
        <AnnouncementFlashModal
            title={title}
            errors={dataToList}
            errorNumber={dataNumber}
            timer={timer}
        />
      </React.Fragment>
  );
}
