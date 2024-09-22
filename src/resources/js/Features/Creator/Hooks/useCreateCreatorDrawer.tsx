import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { useDisclosure } from '@chakra-ui/react';
import { ReactNode } from 'react';
import { CreateCreatorDrawer } from '../Components/CreateCreatorDrawer';
import { CreatorFormData } from '../Types';

type Props = {
  buttonLabel?: ReactNode;
  onStoreSuccess?: (creator: CreatorFormData) => void;
};

export function useCreateCreatorDrawer({ buttonLabel, onStoreSuccess }: Props) {
  const disclosure = useDisclosure();
  const createCreatorDrawer = (
    <CreateCreatorDrawer
      isOpen={disclosure.isOpen}
      onClose={disclosure.onClose}
      onStoreSuccess={onStoreSuccess}
    />
  );
  const createCreatorDrawerOpenButton = !!buttonLabel && (
    <PrimaryButton onClick={disclosure.onOpen}>{buttonLabel}</PrimaryButton>
  );

  return {
    ...disclosure,
    createCreatorDrawer,
    createCreatorDrawerOpenButton,
  };
}
