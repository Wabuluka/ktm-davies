import { useCreateCreationTypeDrawer } from '@/Features/Creator';
import { useSelectInput } from '@/Hooks/Form/useSelectInput';
import { DataFetchError } from '@/UI/Components/Feedback/DataFetchError';
import { LoadingSpinner } from '@/UI/Components/Feedback/LoadingSpinner';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import {
  Button,
  ButtonGroup,
  Flex,
  RadioGroup,
  Spacer,
  Text,
  VStack,
} from '@chakra-ui/react';
import { useCallback, useId, useState } from 'react';
import { CreationTypeEventListenerProvider } from '../Contexts/CreationTypeEventCallbackContext';
import { useIndexCreationType } from '../Hooks/useIndexCreationType';
import { CreationType } from '../Types';
import { CreationTypeList } from './CreationTypeList';

type Props = {
  isOpen: boolean;
  onClose: () => void;
  onSubmit: (name: CreationType['name']) => void;
};

export function SelectCreationTypeDrawer({ isOpen, onClose, onSubmit }: Props) {
  const formId = useId();
  const { data: creationTypes, isLoading, isError } = useIndexCreationType();
  const { createCreationTypeDrawer, createCreationTypeDrawerOpenButton } =
    useCreateCreationTypeDrawer({
      buttonLabel: 'Create',
    });
  const [selectedType, setSelectedType] = useState<string>();
  const radioInput = {
    value: selectedType,
    ...useSelectInput((value) => setSelectedType(value)),
  };

  const handleDeleteSuccess = useCallback(
    (creationTypeName: string) => {
      if (selectedType === creationTypeName) {
        setSelectedType(undefined);
      }
    },
    [selectedType],
  );
  function handleSubmit(e: React.FormEvent<HTMLFormElement>): void {
    e.preventDefault();
    e.stopPropagation();
    if (!selectedType) {
      return;
    }
    onSubmit(selectedType);
    setSelectedType(undefined);
    onClose();
  }

  return (
    <CreationTypeEventListenerProvider onDeleteSuccess={handleDeleteSuccess}>
      <Drawer isOpen={isOpen} onClose={onClose}>
        <Text>Select Creation Type</Text>
        <VStack align="stretch" spacing={8}>
          {isLoading ? (
            <LoadingSpinner />
          ) : isError || !creationTypes ? (
            <DataFetchError />
          ) : (
            <form onSubmit={handleSubmit} id={formId}>
              <RadioGroup {...radioInput}>
                <CreationTypeList
                  creationTypes={creationTypes}
                  selectType="radio"
                />
              </RadioGroup>
            </form>
          )}
        </VStack>
        <Flex w="100%">
          {createCreationTypeDrawerOpenButton}
          <Spacer />
          <ButtonGroup>
            <Button onClick={onClose}>Back</Button>
            <PrimaryButton
              type="submit"
              form={formId}
              isDisabled={!selectedType}
            >
              Save
            </PrimaryButton>
          </ButtonGroup>
        </Flex>
      </Drawer>
      {createCreationTypeDrawer}
    </CreationTypeEventListenerProvider>
  );
}
