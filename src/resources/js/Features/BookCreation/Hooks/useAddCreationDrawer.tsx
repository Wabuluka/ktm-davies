import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { useDisclosure } from '@chakra-ui/react';
import { ReactNode } from 'react';
import { AddCreationDrawer } from '../Components/AddCreationDrawer';

type Props = {
  buttonLabel?: ReactNode;
};

export function useAddCreationDrawer({ buttonLabel }: Props) {
  const disclosure = useDisclosure();
  const addCreationDrawer = (
    <AddCreationDrawer
      isOpen={disclosure.isOpen}
      onClose={disclosure.onClose}
    />
  );
  const addCreationDrawerOpenButton = !!buttonLabel && (
    <PrimaryButton onClick={disclosure.onOpen}>{buttonLabel}</PrimaryButton>
  );

  return {
    ...disclosure,
    addCreationDrawer,
    addCreationDrawerOpenButton,
  };
}
