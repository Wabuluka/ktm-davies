import { isLaravelValidationError } from '@/Features/Misc/Api/Utils/isLaravelValidationError';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Button, ButtonGroup, Text, useDisclosure } from '@chakra-ui/react';
import { FC, useRef, useState } from 'react';
import { QueryKey, useQueryClient } from 'react-query';
import { Form } from './Form';
import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import { StoryFormData } from '@/Features/Story';
import { useCreateStoryMutation } from '@/Features/Story/Hooks/useCreateStoryMutation';

type Props = {
  queryKey: QueryKey;
  renderOpenDrawerElement: (onOpen: () => void) => JSX.Element;
};

export const CreateStoryDrawer: FC<Props> = ({
  queryKey,
  renderOpenDrawerElement,
}) => {
  const { isOpen, onOpen, onClose } = useDisclosure();
  const [errors, setErrors] = useState<Record<string, string[]>>();
  const createStoryMutation = useCreateStoryMutation();
  const firstInput = useRef(null);
  const queryClient = useQueryClient();
  const storyQueryKeys = useQueryKeys().stories;
  const storyIndexQueryKey = storyQueryKeys.all;

  const handleClose = () => {
    setErrors(undefined);
    onClose();
  };

  function handleSubmit(formData: StoryFormData) {
    createStoryMutation.mutate(formData, {
      onSuccess: () => {
        queryClient.invalidateQueries(queryKey);
        queryClient.invalidateQueries(storyIndexQueryKey);
        handleClose();
      },
      onError: (error) => {
        isLaravelValidationError(error) &&
          setErrors(error?.response?.data?.errors);
      },
    });
  }

  return (
    <>
      {renderOpenDrawerElement(onOpen)}
      <Drawer
        isOpen={isOpen}
        onClose={handleClose}
        initialFocusRef={firstInput}
      >
        <Text>Create story</Text>
        <Form
          id="story-form"
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
            isLoading={createStoryMutation.isLoading}
            form="story-form"
          >
            Save
          </PrimaryButton>
        </ButtonGroup>
      </Drawer>
    </>
  );
};
