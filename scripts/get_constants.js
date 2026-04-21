export async function fetchConstants() {
    try {
        const response = await fetch("item/get_constants_service");
        if (!response.ok) throw new Error("Network response was not ok");

        const data = await response.json();
        return data;
    } catch (error) {
        console.error("Fetch failed", error);
    }
}
