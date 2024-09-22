import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { useDisclosure } from '@chakra-ui/react';
import { ReactNode } from 'react';
import { CreateCreationTypeDrawer } from '../Components/CreateCreationTypeDrawer';

type Props = {
  buttonLabel?: ReactNode;
};

export function useCreateCreationTypeDrawer({ buttonLabel }: Props) {
  const disclosure = useDisclosure();
  const createCreationTypeDrawer = (
    <CreateCreationTypeDrawer
      isOpen={disclosure.isOpen}
      onClose={disclosure.onClose}
    />
  );
  const createCreationTypeDrawerOpenButton = !!buttonLabel && (
    <PrimaryButton onClick={disclosure.onOpen}>{buttonLabel}</PrimaryButton>
  );

  return {
    ...disclosure,
    createCreationTypeDrawer,
    createCreationTypeDrawerOpenButton,
  };
}
