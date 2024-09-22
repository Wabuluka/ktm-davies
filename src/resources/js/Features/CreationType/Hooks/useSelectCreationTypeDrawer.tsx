import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { useDisclosure } from '@chakra-ui/react';
import { ReactNode } from 'react';
import { SelectCreationTypeDrawer } from '../Components/SelectCreationTypeDrawer';
import { CreationType } from '../Types';

type Props = {
  buttonLabel?: ReactNode;
  onSubmit: (creation: CreationType['name']) => void;
};

export function useSelectCreationTypeDrawer({ buttonLabel, onSubmit }: Props) {
  const disclosure = useDisclosure();
  const selectCreationTypeDrawer = (
    <SelectCreationTypeDrawer
      isOpen={disclosure.isOpen}
      onClose={disclosure.onClose}
      onSubmit={onSubmit}
    />
  );
  const selectCreationTypeDrawerOpenButton = !!buttonLabel && (
    <PrimaryButton onClick={disclosure.onOpen}>{buttonLabel}</PrimaryButton>
  );

  return {
    ...disclosure,
    selectCreationTypeDrawer,
    selectCreationTypeDrawerOpenButton,
  };
}
