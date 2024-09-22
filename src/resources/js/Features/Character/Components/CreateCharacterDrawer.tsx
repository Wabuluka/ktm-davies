import { isLaravelValidationError } from '@/Features/Misc/Api/Utils/isLaravelValidationError';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Button, ButtonGroup, Text, useDisclosure } from '@chakra-ui/react';
import { FC, useState, useRef } from 'react';
import { Form } from './Form';
import { QueryKey, useQueryClient } from 'react-query';
import { useCreateCharacterMutation } from '../Hooks/useCreateCharacterMutation';
import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import { CharacterFormData } from '@/Features/Character';

type Props = {
  queryKey: QueryKey;
  renderOpenDrawerElement: (onOpen: () => void) => JSX.Element;
};

export const CreateCharacterDrawer: FC<Props> = ({
  queryKey,
  renderOpenDrawerElement,
}) => {
  const { isOpen, onOpen, onClose } = useDisclosure();
  const [errors, setErrors] = useState<Record<string, string[]>>();
  const createCharacterMutation = useCreateCharacterMutation();
  const firstInput = useRef(null);
  const queryClient = useQueryClient();
  const characterQueryKeys = useQueryKeys().characters;
  const characterIndexQueryKey = characterQueryKeys.all;

  const handleClose = () => {
    setErrors(undefined);
    onClose();
  };

  const handleSubmit = (character: CharacterFormData) => {
    createCharacterMutation.mutate(character, {
      onSuccess: () => {
        queryClient.invalidateQueries(queryKey);
        queryClient.invalidateQueries(characterIndexQueryKey);
        handleClose();
      },
      onError: (error) => {
        isLaravelValidationError(error) &&
          setErrors(error?.response?.data?.errors);
      },
    });
  };

  return (
    <>
      {renderOpenDrawerElement(onOpen)}
      <Drawer
        isOpen={isOpen}
        onClose={handleClose}
        initialFocusRef={firstInput}
      >
        <Text>Create a character</Text>

        <Form
          id="character-form"
          errors={errors}
          onSubmit={handleSubmit}
          initialFocusRef={firstInput}
        />

        <ButtonGroup>
          <Button variant="outline" onClick={handleClose}>
            Back
          </Button>
          <PrimaryButton
            type="submit"
            isLoading={createCharacterMutation.isLoading}
            form="character-form"
          >
            Save
          </PrimaryButton>
        </ButtonGroup>
      </Drawer>
    </>
  );
};
