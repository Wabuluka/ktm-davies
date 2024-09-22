import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Button, ButtonGroup, Text, useDisclosure } from '@chakra-ui/react';
import { FC, useState, useRef } from 'react';
import { Story, StoryFormData } from '@/Features/Story';
import { DataFetchError } from '@/UI/Components/Feedback/DataFetchError';
import { LoadingSpinner } from '@/UI/Components/Feedback/LoadingSpinner';
import { Form } from './Form';
import { useShowStoryQuery } from '@/Features/Story/Hooks/useShowStoryQuery';
import { useQueryClient } from 'react-query';
import { isLaravelValidationError } from '@/Features/Misc/Api/Utils/isLaravelValidationError';
import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import { DangerButton } from '@/UI/Components/Form/Button/DangerButton';
import { useEditStoryMutation } from '@/Features/Story/Hooks/useEditStoryMutation';
import { useDeleteStoryMutation } from '@/Features/Story/Hooks/useDeleteStoryMutation';

type Props = {
  story: Story;
  onStoryDeleted: (id: number) => void;
  renderOpenDrawerElement: (onOpen: () => void) => JSX.Element;
};

export const EditStoryDrawer: FC<Props> = ({
  story,
  onStoryDeleted,
  renderOpenDrawerElement,
}) => {
  const { isOpen, onOpen, onClose } = useDisclosure();

  const [errors, setErrors] = useState<Record<string, string[]>>();

  const editStoryMutation = useEditStoryMutation();
  const deleteStoryMutation = useDeleteStoryMutation();

  const firstInput = useRef(null);

  const storyQueryKeys = useQueryKeys().stories;
  const storyIndexQueryKey = storyQueryKeys.all;
  const storyShowQueryKey = storyQueryKeys.show(story.id);

  const { isLoading, isError } = useShowStoryQuery(story.id, {
    enabled: isOpen,
  });

  const queryClient = useQueryClient();

  const handleClose = () => {
    setErrors(undefined);
    onClose();
  };

  const handleDelete = (e: React.MouseEvent<HTMLButtonElement, MouseEvent>) => {
    e.preventDefault();

    if (!story) return;

    if (window.confirm(`Are you sure to delete ${story.title}？`)) {
      deleteStoryMutation.mutate(story.id, {
        onSuccess: () => {
          queryClient.invalidateQueries(storyIndexQueryKey);
          queryClient.removeQueries(storyShowQueryKey);
          onStoryDeleted(story.id);
          handleClose();
        },
        onError: (error) => {
          isLaravelValidationError(error) &&
            setErrors(error?.response?.data?.errors);
        },
      });
    }
  };

  function handleSubmit(formData: StoryFormData) {
    editStoryMutation.mutate(
      { id: story.id, ...formData },
      {
        onSuccess: () => {
          queryClient.invalidateQueries(storyIndexQueryKey);
          queryClient.invalidateQueries(storyShowQueryKey);
          handleClose();
        },
        onError: (error) => {
          isLaravelValidationError(error) &&
            setErrors(error?.response?.data?.errors);
        },
      },
    );
  }

  return (
    <>
      {renderOpenDrawerElement(onOpen)}
      <Drawer
        isOpen={isOpen}
        onClose={handleClose}
        initialFocusRef={firstInput}
      >
        <Text>Edit Story</Text>

        {isLoading ? (
          <LoadingSpinner />
        ) : isError || !story ? (
          <DataFetchError />
        ) : (
          <Form
            id="story-form"
            story={story}
            errors={errors}
            onSubmit={handleSubmit}
            initialFocusRef={firstInput}
          />
        )}

        <ButtonGroup>
          <Button variant="outline" onClick={handleClose}>
            Back
          </Button>
          <DangerButton
            onClick={handleDelete}
            isLoading={deleteStoryMutation.isLoading}
          >
            Delete
          </DangerButton>
          <PrimaryButton type="submit" form="story-form" isDisabled={isLoading}>
            Save
          </PrimaryButton>
        </ButtonGroup>
      </Drawer>
    </>
  );
};
