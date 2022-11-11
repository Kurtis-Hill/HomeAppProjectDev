import * as React from 'react';

import { AnnouncementErrorFlashModal } from '../../Components/Modals/AnnouncementErrorFlashModal';

export function BuildAnnouncementErrorFlashModal(
    props: {
      title: string;
      errors: string[];
      errorNumber: number;
      timer?: number|null;
    }
  ) {
  const title: string = props.title
  const errors: Array<string> = props.errors
  const errorNumber: number = props.errorNumber
  const timer: number = props.timer ?? 80;

  return (
        <AnnouncementErrorFlashModal
            title={title}
            errors={errors}
            errorNumber={errorNumber}
            timer={timer}
        />
  );
}
