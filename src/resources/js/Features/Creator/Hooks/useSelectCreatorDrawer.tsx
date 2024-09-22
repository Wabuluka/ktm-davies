import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { useDisclosure } from '@chakra-ui/react';
import { ReactNode } from 'react';
import { SelectCreatorDrawer } from '../Components/SelectCreatorDrawer';
import { Creator } from '../Types';

type Props = {
  buttonLabel?: ReactNode;
  selectable?: boolean | ((creator: Creator) => boolean);
  onSubmit: (creation: Creator) => void;
};

export function useSelectCreatorDrawer({
  buttonLabel,
  selectable,
  onSubmit,
}: Props) {
  const disclosure = useDisclosure();
  const selectCreatorDrawer = (
    <SelectCreatorDrawer
      isOpen={disclosure.isOpen}
      onClose={disclosure.onClose}
      selectable={selectable}
      onSubmit={onSubmit}
    />
  );
  const selectCreatorDrawerOpenButton = !!buttonLabel && (
    <PrimaryButton onClick={disclosure.onOpen}>{buttonLabel}</PrimaryButton>
  );

  return {
    ...disclosure,
    selectCreatorDrawer,
    selectCreatorDrawerOpenButton,
  };
}
