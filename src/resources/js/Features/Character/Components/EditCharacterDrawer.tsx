import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import { isLaravelValidationError } from '@/Features/Misc/Api/Utils/isLaravelValidationError';
import { useShowCharacterQuery } from '../Hooks/useShowCharacterQuery';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { DangerButton } from '@/UI/Components/Form/Button/DangerButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Button, ButtonGroup, Text, useDisclosure } from '@chakra-ui/react';
import { FC } from 'react';
import { Form } from './Form';
import { useQueryClient } from 'react-query';
import { useState, useRef } from 'react';
import { useEditCharacterMutation } from '../Hooks/useEditCharacterMutation';
import { useDeleteCharacterMutation } from '../Hooks/useDeleteCharacterMutation';
import { Character, CharacterFormData } from '@/Features/Character';
import { LoadingSpinner } from '@/UI/Components/Feedback/LoadingSpinner';
import { DataFetchError } from '@/UI/Components/Feedback/DataFetchError';

type Props = {
  character: Character;
  onCharacterDeleted: (id: number) => void;
  renderOpenDrawerElement: (onOpen: () => void) => JSX.Element;
};

export const EditCharacterDrawer: FC<Props> = ({
  character,
  onCharacterDeleted,
  renderOpenDrawerElement,
}) => {
  const { isOpen, onOpen, onClose } = useDisclosure();

  const [errors, setErrors] = useState<Record<string, string[]>>();

  const editCharacterMutation = useEditCharacterMutation();
  const deleteCharacterMutation = useDeleteCharacterMutation();

  const { id, name } = character;

  const characterQueryKeys = useQueryKeys().characters;
  const characterIndexQueryKey = characterQueryKeys.all;
  const characterShowQueryKey = characterQueryKeys.show(id);

  const { isLoading, isError } = useShowCharacterQuery(id, {
    enabled: isOpen,
  });

  const queryClient = useQueryClient();

  const handleClose = () => {
    setErrors(undefined);
    onClose();
  };

  const handleDelete = (e: React.MouseEvent<HTMLButtonElement, MouseEvent>) => {
    e.preventDefault();

    if (!character) return;

    if (window.confirm(`$Do you delete {name}？`)) {
      deleteCharacterMutation.mutate(id, {
        onSuccess: () => {
          queryClient.invalidateQueries(characterIndexQueryKey);
          queryClient.removeQueries(characterShowQueryKey);
          onCharacterDeleted(id);
          handleClose();
        },
        onError: (error) => {
          isLaravelValidationError(error) &&
            setErrors(error?.response?.data?.errors);
        },
      });
    }
  };

  const handleSubmit = (formData: CharacterFormData) => {
    editCharacterMutation.mutate(
      { id, ...formData },
      {
        onSuccess: () => {
          queryClient.invalidateQueries(characterIndexQueryKey);
          queryClient.invalidateQueries(characterShowQueryKey);
          handleClose();
        },
        onError: (error) => {
          isLaravelValidationError(error) &&
            setErrors(error?.response?.data?.errors);
        },
      },
    );
  };

  const firstInput = useRef(null);

  return (
    <>
      {renderOpenDrawerElement(onOpen)}
      <Drawer isOpen={isOpen} onClose={onClose} initialFocusRef={firstInput}>
        <Text>Edit a character</Text>

        {isLoading ? (
          <LoadingSpinner />
        ) : isError || !character ? (
          <DataFetchError />
        ) : (
          <Form
            id="character-form"
            character={character}
            errors={errors}
            onSubmit={handleSubmit}
            initialFocusRef={firstInput}
          />
        )}

        <ButtonGroup>
          <Button variant="outline" onClick={onClose}>
            Back
          </Button>
          <DangerButton
            onClick={handleDelete}
            isLoading={deleteCharacterMutation.isLoading}
          >
            Delete
          </DangerButton>
          <PrimaryButton
            type="submit"
            form="character-form"
            isDisabled={isLoading}
          >
            Save
          </PrimaryButton>
        </ButtonGroup>
      </Drawer>
    </>
  );
};
