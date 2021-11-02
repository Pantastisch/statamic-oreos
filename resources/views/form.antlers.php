<form action="{{ route:statamic.oreos.save }}" method="post">
    <input type="hidden" name="_token" value="<?php echo csrf_token() ?>">

    <div class="flex flex-col gap-2 my-8">
        {{ oreos:groups }}
            <div>
                <label class="flex items-baseline">
                    <input
                        type="checkbox"
                        name="oreos[]"
                        value="{{ handle }}"
                        {{ checked ? 'checked' : '' }}
                        {{ required ? 'required disabled' : '' }}
                        class="mr-2 {{ required ? ' opacity-40' : '' }}"
                    >
                    <div>
                        <p class="font-semibold">{{ title }}</p>
                        {{ if description }}
                            <p class="text-sm text-gray-600">{{ description }}</p>
                        {{ /if }}
                    </div>
                </label>
            </div>
        {{ /oreos:groups }}
    </div>

    <div class="flex gap-2">
        <button class="py-1 px-2 bg-gray-200 border hover:border-current" type="submit" name="action" value="save">
            Save
        </button>
        <button class="py-1 px-2 border hover:border-current" type="submit" name="action" value="accept-all">
            Accept all
        </button>
        <button class="py-1 px-2 border hover:border-current" type="reset" onclick="removeOreosPopup()">
            Cancel
        </button>
    </div>

    <script>
        function removeOreosPopup() {
            const el = document.getElementById('{{ popupId ?? "oreos-popup" }}');
            if (el) el.parentNode.removeChild(el);
        }
    </script>

</form>